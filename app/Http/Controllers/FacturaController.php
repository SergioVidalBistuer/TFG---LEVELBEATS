<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Compra;

/**
 * Class FacturaController
 *
 * Controlador para la gestión y visualización de las facturas generadas
 * a partir de las compras.
 */
class FacturaController extends Controller
{
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

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE FACTURAS DEL USUARIO
    |--------------------------------------------------------------------------
    */
    /**
     * Muestra un listado de las facturas del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = Factura::with(['compra.comprador', 'compra.beats', 'compra.colecciones'])
            ->orderBy('id', 'desc');

        if (!$this->isAdmin()) {
            $query->whereHas('compra', function ($q) {
                $q->where('id_usuario_comprador', $this->userId());
            });
        }

        $facturas = $query->get();

        return view('factura.index', compact('facturas'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE DE UNA FACTURA
    |--------------------------------------------------------------------------
    */
    /**
     * Muestra el detalle de una factura específica.
     *
     * @param  int  $id  Identificador único de la factura.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la factura no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function detail($id)
    {
        $factura = Factura::with(['compra.comprador', 'compra.beats', 'compra.colecciones'])
            ->findOrFail($id);

        // Solo el propietario o admin puede ver
        if (!$this->isAdmin() && $factura->compra->id_usuario_comprador !== $this->userId()) {
            abort(403);
        }

        return view('factura.detail', compact('factura'));
    }
}
