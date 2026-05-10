<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Factura;
use App\Models\Usuario;
use App\Support\CarritoCompra;
use App\Support\LicenciaCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    private function adminId(): int
    {
        $admin = Usuario::whereHas('roles', function ($q) {
            $q->where('nombre_rol', 'admin')->where('usuario_rol.rol_activo', 1);
        })->first();

        if (!$admin) {
            throw new \RuntimeException('No se encontró ningún usuario administrador en la base de datos.');
        }

        return $admin->id;
    }

    private function userId(): ?int
    {
        return auth()->id();
    }

    private function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->esAdmin();
    }

    private function canView(Compra $compra): bool
    {
        return $this->isAdmin() || $compra->id_usuario_comprador === $this->userId();
    }

    public function index()
    {
        $query = Compra::with(['beats', 'colecciones', 'comprador', 'factura', 'servicios', 'detalles.licencia'])
            ->orderBy('id', 'desc');

        if (!$this->isAdmin()) {
            $query->where('id_usuario_comprador', $this->userId());
        }

        $compras = $query->get();

        return view('compra.index', compact('compras'));
    }

    public function detail($id)
    {
        $compra = Compra::with(['beats', 'colecciones.beats', 'comprador', 'factura', 'servicios', 'contrato', 'detalles.licencia'])
            ->findOrFail($id);

        if (!$this->canView($compra)) {
            abort(403);
        }

        return view('compra.detail', compact('compra'));
    }

    public function showCheckout()
    {
        $cart = CarritoCompra::normalizar(session()->get('cart'));
        session()->put('cart', $cart);

        if (empty($cart['beats']) && empty($cart['colecciones']) && empty($cart['servicios'])) {
            return redirect()->route('carrito.index')
                ->with('status', 'El carrito está vacío.');
        }

        $items = CarritoCompra::items($cart);

        if ($items['beats']->isEmpty() && $items['colecciones']->isEmpty() && $items['servicios']->isEmpty()) {
            return redirect()->route('carrito.index')
                ->with('status', 'Los productos del carrito ya no están disponibles.');
        }

        $usuario = auth()->user();
        $total = $items['total'];

        return view('compra.checkout', compact('usuario', 'total', 'items'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'metodo_de_pago' => 'required|in:paypal,tarjeta,transferencia',
            'calle'          => 'nullable|string|max:255',
            'localidad'      => 'nullable|string|max:100',
            'provincia'      => 'nullable|string|max:100',
            'pais'           => 'nullable|string|max:100',
            'codigo_postal'  => 'nullable|string|max:20',
        ], [
            'metodo_de_pago.required' => 'Selecciona un método de pago.',
            'metodo_de_pago.in' => 'El método de pago seleccionado no es válido.',
        ]);

        $cart = CarritoCompra::normalizar(session()->get('cart'));

        if (empty($cart['beats']) && empty($cart['colecciones']) && empty($cart['servicios'])) {
            return redirect()->route('carrito.index')
                ->with('status', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $usuario = auth()->user();

            $usuario->update([
                'calle'         => $request->calle ?? $usuario->calle,
                'localidad'     => $request->localidad ?? $usuario->localidad,
                'provincia'     => $request->provincia ?? $usuario->provincia,
                'pais'          => $request->pais ?? $usuario->pais,
                'codigo_postal' => $request->codigo_postal ?? $usuario->codigo_postal,
            ]);

            $items = CarritoCompra::items($cart);
            $lineasProducto = $items['beats']->merge($items['colecciones']);
            $lineasServicio = $items['servicios'];
            $lineas = $lineasProducto->merge($lineasServicio);

            if ($lineas->isEmpty()) {
                throw new \RuntimeException('Uno o varios productos del carrito ya no están disponibles públicamente.');
            }

            if ($lineasServicio->isNotEmpty() && ($items['beats']->isNotEmpty() || $items['colecciones']->isNotEmpty() || $lineasServicio->count() > 1)) {
                throw new \RuntimeException('El pago de un servicio debe tramitarse de forma individual.');
            }

            foreach ($lineasProducto as $linea) {
                if ($linea['producto']->id_usuario === $this->userId()) {
                    throw new \RuntimeException('No puedes comprar productos publicados por tu propia cuenta.');
                }

                if ($linea['licencia']->tipo_licencia === 'exclusiva' && $linea['exclusiva_vendida']) {
                    throw new \RuntimeException('La licencia exclusiva de "' . $linea['nombre_producto'] . '" ya está vendida.');
                }
            }

            foreach ($lineasServicio as $linea) {
                $proyecto = $linea['proyecto'];

                if ($proyecto->id_usuario !== $this->userId()) {
                    throw new \RuntimeException('No puedes pagar un servicio solicitado por otro usuario.');
                }

                if ($proyecto->estado_proyecto === 'cancelado' || !empty($proyecto->cancelado_at)) {
                    throw new \RuntimeException('Este servicio está cancelado.');
                }

                if (empty($proyecto->ingeniero_aceptado_at)) {
                    throw new \RuntimeException('El ingeniero todavía no ha aceptado este servicio.');
                }

                if (!empty($proyecto->id_compra) || !empty($proyecto->cliente_aceptado_at)) {
                    throw new \RuntimeException('Este servicio ya está pagado.');
                }
            }

            $total = $items['total'];
            $vendedorId = $lineasServicio->isNotEmpty()
                ? $lineasServicio->first()['producto']->id_usuario
                : $this->adminId();

            $compra = Compra::create([
                'id_usuario_comprador' => $this->userId(),
                'id_usuario_vendedor'  => $vendedorId,
                'importe_total'        => $total,
                'metodo_de_pago'       => $request->metodo_de_pago,
                'estado_compra'        => 'pagada',
                'url_contrato_pdf'     => null,
                'fecha_compra'         => now(),
            ]);

            $beatIdsDirectos = $items['beats']->pluck('producto.id')->unique()->values()->all();
            if (!empty($beatIdsDirectos)) {
                $compra->beats()->syncWithoutDetaching($beatIdsDirectos);
            }

            $coleccionIds = $items['colecciones']->pluck('producto.id')->unique()->values()->all();
            if (!empty($coleccionIds)) {
                $compra->colecciones()->syncWithoutDetaching($coleccionIds);

                $beatsIncluidos = $items['colecciones']
                    ->flatMap(fn ($linea) => $linea['producto']->beats->pluck('id'))
                    ->merge($beatIdsDirectos)
                    ->unique()
                    ->values()
                    ->all();

                if (!empty($beatsIncluidos)) {
                    $compra->beats()->syncWithoutDetaching($beatsIncluidos);
                }
            }

            if ($lineasServicio->isNotEmpty()) {
                $lineaServicio = $lineasServicio->first();
                $proyectoServicio = $lineaServicio['proyecto'];

                $compra->servicios()->syncWithoutDetaching([$lineaServicio['producto']->id]);

                $proyectoServicio->update([
                    'cliente_aceptado_at' => now(),
                    'id_compra' => $compra->id,
                    'estado_proyecto' => 'en_proceso',
                ]);
            }

            foreach ($lineasProducto as $linea) {
                CompraDetalle::create([
                    'id_compra' => $compra->id,
                    'tipo_producto' => $linea['tipo'],
                    'id_producto' => $linea['producto']->id,
                    'id_licencia' => $linea['licencia']->id,
                    'precio_base_producto' => $linea['precio_base'],
                    'precio_licencia' => $linea['precio_licencia'],
                    'precio_final' => $linea['precio_final'],
                    'nombre_producto_snapshot' => $linea['nombre_producto'],
                    'nombre_licencia_snapshot' => $linea['spec']['nombre'],
                    'formato_incluido_snapshot' => LicenciaCompra::formato($linea['licencia']),
                    'derechos_snapshot' => $linea['spec']['derechos'],
                    'fecha' => now(),
                ]);
            }

            $iva = 0.21;
            $baseImponible = round($total / (1 + $iva), 2);
            $impuestos = round($total - $baseImponible, 2);

            Factura::create([
                'id_compra'         => $compra->id,
                'numero_factura'    => 'LB-' . str_pad($compra->id, 6, '0', STR_PAD_LEFT),
                'base_imponible'    => $baseImponible,
                'importe_impuestos' => $impuestos,
                'importe_total'     => $total,
                'url_factura_pdf'   => null,
                'pago_confirmado'   => true,
                'fecha_emision'     => now(),
            ]);

            DB::commit();

            session()->forget('cart');

            return redirect()->route('compra.index')
                ->with('status', 'Compra realizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('carrito.index')
                ->with('status', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }
}
