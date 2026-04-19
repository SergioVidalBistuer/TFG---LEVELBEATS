<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Compra;

class FacturaController extends Controller
{
    private function userId(): ?int
    {
        return auth()->id();
    }

    private function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->esAdmin();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE FACTURAS DEL USUARIO
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $query = Factura::with(['compra.comprador', 'compra.beats', 'compra.servicios'])
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
    public function detail($id)
    {
        $factura = Factura::with(['compra.comprador', 'compra.beats', 'compra.servicios'])
            ->findOrFail($id);

        // Solo el propietario o admin puede ver
        if (!$this->isAdmin() && $factura->compra->id_usuario_comprador !== $this->userId()) {
            abort(403);
        }

        return view('factura.detail', compact('factura'));
    }
}
