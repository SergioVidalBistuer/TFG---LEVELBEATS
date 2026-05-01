<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Factura;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{

    /**
     * Obtiene el ID del admin dinámicamente (no hardcodeado).
     * Busca el primer usuario con rol 'admin' activo.
     */
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

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE COMPRAS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $query = Compra::with(['beats', 'comprador', 'factura', 'servicios'])
            ->orderBy('id', 'desc');

        if (!$this->isAdmin()) {
            $query->where('id_usuario_comprador', $this->userId());
        }

        $compras = $query->get();

        return view('compra.index', compact('compras'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */
    public function detail($id)
    {
        $compra = Compra::with(['beats', 'comprador', 'factura', 'servicios', 'contrato'])
            ->findOrFail($id);

        if (!$this->canView($compra)) {
            abort(403);
        }

        return view('compra.detail', compact('compra'));
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT DESDE CARRITO
    |--------------------------------------------------------------------------
    */
    public function showCheckout()
    {
        $cart = session()->get('cart');

        if (!$cart || empty($cart['beats'])) {
            return redirect()->route('carrito.index')
                ->with('status', 'El carrito está vacío o no contiene beats válidos.');
        }

        $usuario = auth()->user();
        $beatIds = array_keys($cart['beats'] ?? []);
        $total = Beat::whereIn('id', $beatIds)->sum('precio_base_licencia');

        return view('compra.checkout', compact('usuario', 'total'));
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
        ]);
        
        $cart = session()->get('cart');

        if (!$cart || empty($cart['beats'])) {
            return redirect()->route('carrito.index')
                ->with('status', 'El carrito está vacío o no contiene beats válidos');
        }

        DB::beginTransaction();

        try {
            $usuario = auth()->user();
            
            // Actualizar datos fiscales del usuario si se han proporcionado en el checkout
            $usuario->update([
                'calle'         => $request->calle ?? $usuario->calle,
                'localidad'     => $request->localidad ?? $usuario->localidad,
                'provincia'     => $request->provincia ?? $usuario->provincia,
                'pais'          => $request->pais ?? $usuario->pais,
                'codigo_postal' => $request->codigo_postal ?? $usuario->codigo_postal,
            ]);

            $beatIds = array_keys($cart['beats'] ?? []);

            $importeBeats = Beat::whereIn('id', $beatIds)->sum('precio_base_licencia');

            $total = $importeBeats;

            $compra = Compra::create([
                'id_usuario_comprador' => $this->userId(),
                'id_usuario_vendedor'  => $this->adminId(),
                'importe_total'        => $total,
                'metodo_de_pago'       => $request->metodo_de_pago,
                'estado_compra'        => 'pagada',
                'url_contrato_pdf'     => null,
                'fecha_compra'         => now(),
            ]);

            if (!empty($beatIds)) {
                $compra->beats()->attach($beatIds);
            }

            // Generar factura automáticamente (dentro de la transacción)
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
