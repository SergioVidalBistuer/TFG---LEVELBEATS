<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Factura;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FacturaPdfService
{
    public function generar(Factura $factura, bool $forzar = false): string
    {
        $factura->loadMissing([
            'compra.comprador',
            'compra.detalles.licencia',
            'compra.beats',
            'compra.colecciones',
            'compra.servicios',
        ]);

        $rutaPublica = $factura->url_factura_pdf;
        $rutaDisco = $rutaPublica ? $this->publicPathToDiskPath($rutaPublica) : null;

        if (!$forzar && $rutaDisco && Storage::disk('public')->exists($rutaDisco)) {
            return $rutaPublica;
        }

        $compra = $factura->compra;
        $lineas = $this->lineas($compra);
        $logoDataUri = $this->logoDataUri();

        $html = view('pdf.factura', compact('factura', 'compra', 'lineas', 'logoDataUri'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombre = $this->nombreArchivo($factura);
        $rutaDisco = 'facturas/' . $nombre;
        Storage::disk('public')->put($rutaDisco, $dompdf->output());

        $rutaPublica = 'storage/' . $rutaDisco;
        $factura->forceFill(['url_factura_pdf' => $rutaPublica])->save();

        return $rutaPublica;
    }

    public function lineas(Compra $compra): Collection
    {
        $compra->loadMissing(['detalles.licencia', 'beats', 'colecciones', 'servicios']);

        if ($compra->detalles->isNotEmpty()) {
            return $compra->detalles->map(function ($detalle) {
                $precioBase = (float) ($detalle->precio_base_producto ?? 0);
                $precioLicencia = (float) ($detalle->precio_licencia ?? 0);
                $total = (float) ($detalle->precio_final ?? ($precioBase + $precioLicencia));

                return [
                    'producto' => $detalle->nombre_producto_snapshot ?? 'Producto sin nombre',
                    'tipo' => $this->tipoLegible($detalle->tipo_producto),
                    'licencia' => $detalle->nombre_licencia_snapshot ?? 'Licencia no registrada',
                    'formato' => $detalle->formato_incluido_snapshot ?? '-',
                    'precio_base' => $precioBase,
                    'precio_licencia' => $precioLicencia,
                    'total' => $total,
                ];
            })->values();
        }

        return collect()
            ->merge($compra->beats->map(fn ($beat) => [
                'producto' => $beat->titulo_beat ?? 'Beat sin título',
                'tipo' => 'Beat',
                'licencia' => 'Licencia no registrada',
                'formato' => '-',
                'precio_base' => (float) ($beat->precio_base_licencia ?? 0),
                'precio_licencia' => 0.0,
                'total' => (float) ($beat->precio_base_licencia ?? 0),
            ]))
            ->merge($compra->colecciones->map(fn ($coleccion) => [
                'producto' => $coleccion->titulo_coleccion ?? 'Colección sin título',
                'tipo' => 'Colección',
                'licencia' => 'Licencia no registrada',
                'formato' => '-',
                'precio_base' => (float) ($coleccion->precio ?? 0),
                'precio_licencia' => 0.0,
                'total' => (float) ($coleccion->precio ?? 0),
            ]))
            ->merge($compra->servicios->map(fn ($servicio) => [
                'producto' => $servicio->titulo_servicio ?? 'Servicio sin título',
                'tipo' => 'Servicio',
                'licencia' => '-',
                'formato' => '-',
                'precio_base' => (float) ($servicio->precio_servicio ?? 0),
                'precio_licencia' => 0.0,
                'total' => (float) ($servicio->precio_servicio ?? 0),
            ]))
            ->values();
    }

    private function tipoLegible(?string $tipo): string
    {
        return match ($tipo) {
            'beat' => 'Beat',
            'coleccion' => 'Colección',
            'servicio' => 'Servicio',
            default => $tipo ? ucfirst($tipo) : 'Producto',
        };
    }

    private function nombreArchivo(Factura $factura): string
    {
        $numero = $factura->numero_factura ?: ('LB-' . str_pad((string) $factura->id, 6, '0', STR_PAD_LEFT));
        $numero = preg_replace('/[^A-Za-z0-9_-]+/', '-', $numero);

        return $numero . '.pdf';
    }

    private function publicPathToDiskPath(string $rutaPublica): string
    {
        return str_starts_with($rutaPublica, 'storage/')
            ? substr($rutaPublica, strlen('storage/'))
            : $rutaPublica;
    }

    private function logoDataUri(): ?string
    {
        $path = public_path('media/img/LB-04-pdf.png');

        if (!is_file($path)) {
            $path = public_path('media/img/LB-04.png');
        }

        if (!is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
