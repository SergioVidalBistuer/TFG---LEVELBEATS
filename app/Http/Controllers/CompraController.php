<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class CompraController
 *
 * Controlador para procesar la compra de beats y colecciones,
 * generar facturas y visualizar el historial de compras.
 */
class CompraController extends Controller
{
    private const ADMIN_ID = 1;

    /**
     * Obtiene el ID del usuario actualmente autenticado.
     *
     * @return int|null
     */
    private function userId(): ?int
    {
        return session('usuario_id');
    }

    /**
     * Verifica si el usuario actual es un administrador.
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        return session('rol') === 'admin';
    }

    /**
     * Verifica si el usuario actual puede ver la compra especificada.
     *
     * @param  \App\Models\Compra  $compra
     * @return bool
     */
    private function canView(Compra $compra): bool
    {
        return $this->isAdmin() || $compra->id_usuario_comprador === $this->userId();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE COMPRAS
    |--------------------------------------------------------------------------
    */
    /**
     * Muestra un listado de compras realizadas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = Compra::with(['beats', 'colecciones', 'comprador', 'factura'])
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
    /**
     * Muestra el detalle de una compra específica.
     *
     * @param  int  $id  Identificador único de la compra.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la compra no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function detail($id)
    {
        $compra = Compra::with(['beats', 'colecciones', 'comprador', 'factura'])
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
    /**
     * Procesa la compra de los elementos actuales en el carrito.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkout()
    {
        $cart = session()->get('cart');

        if (!$cart || (empty($cart['beats']) && empty($cart['colecciones']))) {
            return redirect()->route('carrito.index')
                ->with('status', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {

            $beatIds = array_keys($cart['beats'] ?? []);
            $coleccionIds = array_keys($cart['colecciones'] ?? []);

            $importeBeats = Beat::whereIn('id', $beatIds)->sum('precio_base_licencia');
            $importeColecciones = Coleccion::whereIn('id', $coleccionIds)->sum('precio');

            $total = $importeBeats + $importeColecciones;

            $compra = Compra::create([
                'id_usuario_comprador' => $this->userId(),
                'id_usuario_vendedor' => self::ADMIN_ID,
                'importe_total' => $total,
                'metodo_de_pago' => 'paypal',
                'estado_compra' => 'pagada',
                'url_contrato_pdf' => null,
                'fecha_compra' => now(),
            ]);

            if (!empty($beatIds)) {
                $compra->beats()->attach($beatIds);
            }

            if (!empty($coleccionIds)) {
                $compra->colecciones()->attach($coleccionIds);
            }

            DB::commit();

            // Generar factura automáticamente
            $iva = 0.21;
            $baseImponible = round($total / (1 + $iva), 2);
            $impuestos = round($total - $baseImponible, 2);

            Factura::create([
                'id_compra'        => $compra->id,
                'numero_factura'   => 'LB-' . str_pad($compra->id, 6, '0', STR_PAD_LEFT),
                'base_imponible'   => $baseImponible,
                'importe_impuestos'=> $impuestos,
                'importe_total'    => $total,
                'url_factura_pdf'  => null,
                'pago_confirmado'  => true,
                'fecha_emision'    => now(),
            ]);

            session()->forget('cart');

            return redirect()->route('compra.index')
                ->with('status', 'Compra realizada correctamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('carrito.index')
                ->with('status', 'Error al procesar la compra');
        }
    }
}
