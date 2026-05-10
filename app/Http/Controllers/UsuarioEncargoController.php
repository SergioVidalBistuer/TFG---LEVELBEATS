<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Support\CarritoCompra;

class UsuarioEncargoController extends Controller
{
    /**
     * Asegura que el proyecto que se intenta ver pertenezca al cliente actual.
     */
    private function canView(Proyecto $proyecto): bool
    {
        $usuario = auth()->user();
        if ($usuario->esAdmin()) return true;

        return $proyecto->id_usuario === $usuario->id;
    }

    private function isEngineer(Proyecto $proyecto): bool
    {
        return $proyecto->servicio && $proyecto->servicio->id_usuario === auth()->id();
    }

    private function isClient(Proyecto $proyecto): bool
    {
        return $proyecto->id_usuario === auth()->id();
    }

    private function canActOn(Proyecto $proyecto): bool
    {
        return auth()->user()->esAdmin() || $this->isEngineer($proyecto) || $this->isClient($proyecto);
    }

    private function isCanceled(Proyecto $proyecto): bool
    {
        return $proyecto->estado_proyecto === 'cancelado' || !empty($proyecto->cancelado_at);
    }

    private function isPaidOrInProcess(Proyecto $proyecto): bool
    {
        return !empty($proyecto->id_compra)
            || !empty($proyecto->cliente_aceptado_at)
            || in_array($proyecto->estado_proyecto, ['en_proceso', 'en_revision', 'entregado', 'cerrado'], true);
    }

    public function index()
    {
        // Traemos todos los proyectos encargados por el usuario logueado (el cliente)
        // Precargamos la relación de servicio y el ingeniero asociado al servicio
        $proyectos = Proyecto::with(['servicio.usuario'])
            ->where('id_usuario', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return view('usuario.encargos.index', compact('proyectos'));
    }

    public function detail($id)
    {
        $proyecto = Proyecto::with(['servicio.usuario', 'mensajes.emisor', 'archivos.usuario', 'compra.factura'])->findOrFail($id);
        
        if (!$this->canView($proyecto)) {
            abort(403, 'Acceso denegado a este encargo.');
        }

        $archivos = $proyecto->archivos
            ->sortByDesc(fn ($archivo) => optional($archivo->fecha_subida)->timestamp ?? $archivo->id)
            ->values();
        $archivosCliente = $archivos->filter(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) $proyecto->id_usuario)->values();
        $archivosIngeniero = $archivos->filter(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) ($proyecto->servicio->id_usuario ?? 0))->values();
        $archivosSinAutor = $archivos->reject(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) $proyecto->id_usuario || (int) ($archivo->id_usuario ?? 0) === (int) ($proyecto->servicio->id_usuario ?? 0))->values();

        return view('usuario.encargos.detail', compact('proyecto', 'archivos', 'archivosCliente', 'archivosIngeniero', 'archivosSinAutor'));
    }

    public function aceptarIngeniero(Proyecto $proyecto)
    {
        $proyecto->load('servicio');

        if (!$this->isEngineer($proyecto) && !auth()->user()->esAdmin()) {
            abort(403, 'Solo el ingeniero propietario puede aceptar este servicio.');
        }

        if ($this->isCanceled($proyecto) || $this->isPaidOrInProcess($proyecto)) {
            return back()->with('status', 'Este servicio ya no admite aceptación del ingeniero.');
        }

        if (empty($proyecto->ingeniero_aceptado_at)) {
            $proyecto->update([
                'ingeniero_aceptado_at' => now(),
                'estado_proyecto' => 'pendiente_pago_cliente',
            ]);
        }

        return back()->with('status', 'Servicio aceptado por el ingeniero. Queda pendiente la aceptación y pago del cliente.');
    }

    public function aceptarPagar(Proyecto $proyecto)
    {
        $proyecto->load('servicio');

        if (!$this->isClient($proyecto) && !auth()->user()->esAdmin()) {
            abort(403, 'Solo el cliente solicitante puede aceptar y pagar este servicio.');
        }

        if ($this->isCanceled($proyecto)) {
            return back()->with('status', 'Este servicio está cancelado y no puede pagarse.');
        }

        if (empty($proyecto->ingeniero_aceptado_at)) {
            return back()->with('status', 'Pendiente de aceptación del ingeniero.');
        }

        if ($this->isPaidOrInProcess($proyecto)) {
            return back()->with('status', 'Este servicio ya está pagado o en proceso.');
        }

        $cart = CarritoCompra::agregarServicio(CarritoCompra::vacio(), $proyecto->servicio->id, $proyecto->id);
        session()->put('cart', $cart);

        return redirect()
            ->route('compra.checkout.show')
            ->with('status', 'Revisa el servicio, completa tus datos y confirma el pago para iniciar el encargo.');
    }

    public function cancelarServicio(Proyecto $proyecto)
    {
        $proyecto->load('servicio');

        if (!$this->canActOn($proyecto)) {
            abort(403, 'No puedes cancelar este servicio.');
        }

        if ($this->isCanceled($proyecto)) {
            return back()->with('status', 'El servicio ya está cancelado.');
        }

        if ($this->isPaidOrInProcess($proyecto)) {
            return back()->with('status', 'No se puede cancelar un servicio ya aceptado y pagado.');
        }

        $proyecto->update([
            'estado_proyecto' => 'cancelado',
            'cancelado_at' => now(),
            'cancelado_por' => auth()->id(),
        ]);

        return back()->with('status', 'Servicio cancelado correctamente.');
    }
}
