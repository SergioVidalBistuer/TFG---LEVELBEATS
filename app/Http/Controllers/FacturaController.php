<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Compra;
use App\Services\FacturaPdfService;
use Illuminate\Support\Facades\Storage;

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
        $query = Factura::with(['compra.comprador', 'compra.beats', 'compra.colecciones', 'compra.servicios', 'compra.detalles.licencia'])
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
        $factura = Factura::with(['compra.comprador', 'compra.beats', 'compra.colecciones.beats', 'compra.servicios', 'compra.detalles.licencia'])
            ->findOrFail($id);

        // Solo el propietario o admin puede ver
        if (!$this->isAdmin() && $factura->compra->id_usuario_comprador !== $this->userId()) {
            abort(403);
        }

        return view('factura.detail', compact('factura'));
    }

    /*
    |--------------------------------------------------------------------------
    | DESCARGA PDF DE FACTURA
    |--------------------------------------------------------------------------
    */
    public function downloadPdf(Compra $compra, FacturaPdfService $pdfService)
    {
        $compra->loadMissing('factura');

        if (!$this->isAdmin() && $compra->id_usuario_comprador !== $this->userId()) {
            abort(403);
        }

        if (!$compra->factura) {
            abort(404, 'Esta compra no tiene una factura asociada.');
        }

        $rutaPublica = $pdfService->generar($compra->factura);
        $rutaDisco = str_starts_with($rutaPublica, 'storage/')
            ? substr($rutaPublica, strlen('storage/'))
            : $rutaPublica;

        if (!Storage::disk('public')->exists($rutaDisco)) {
            abort(404, 'No se ha podido localizar el PDF de la factura.');
        }

        return response(Storage::disk('public')->get($rutaDisco), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($rutaDisco) . '"',
        ]);
    }
}
