<?php

namespace App\Support;

use App\Models\CompraDetalle;
use App\Models\Licencia;
use Illuminate\Support\Collection;

class LicenciaCompra
{
    public static function tiposPermitidos(): array
    {
        return ['basica', 'premium', 'exclusiva'];
    }

    public static function especificaciones(): array
    {
        return [
            'basica' => [
                'titulo' => 'Licencia Básica',
                'nombre' => 'Licencia Básica no exclusiva',
                'precio' => 29.99,
                'formato' => 'MP3',
                'resumen' => 'Uso no exclusivo para lanzamientos independientes y contenido digital.',
                'derechos' => '50.000 reproducciones, 3.000 copias, 1 videoclip y 5 actuaciones.',
            ],
            'premium' => [
                'titulo' => 'Licencia Premium',
                'nombre' => 'Licencia Premium no exclusiva',
                'precio' => 59.99,
                'formato' => 'MP3 + WAV',
                'resumen' => 'Uso no exclusivo ampliado para lanzamientos profesionales.',
                'derechos' => '250.000 reproducciones, 10.000 copias, 2 videoclips y 25 actuaciones.',
            ],
            'exclusiva' => [
                'titulo' => 'Licencia Exclusiva',
                'nombre' => 'Licencia Exclusiva',
                'precio' => 199.99,
                'formato' => 'MP3 + WAV + STEMS',
                'resumen' => 'Derechos amplios y exclusivos sobre el producto adquirido.',
                'derechos' => 'Reproducción ilimitada y retirada de nuevas ventas exclusivas del mismo producto.',
            ],
        ];
    }

    public static function opciones(): Collection
    {
        $licencias = Licencia::whereIn('tipo_licencia', self::tiposPermitidos())
            ->get()
            ->keyBy('tipo_licencia');

        return collect(self::tiposPermitidos())
            ->map(fn (string $tipo) => $licencias->get($tipo))
            ->filter()
            ->values();
    }

    public static function licenciaBasica(): ?Licencia
    {
        return Licencia::where('tipo_licencia', 'basica')->first();
    }

    public static function spec(?Licencia $licencia): array
    {
        if (!$licencia) {
            return [
                'titulo' => 'Licencia no registrada',
                'nombre' => 'Licencia no registrada',
                'precio' => 0.00,
                'formato' => 'No registrado',
                'resumen' => 'Esta compra es anterior al sistema de licencias por producto.',
                'derechos' => 'No registrado',
            ];
        }

        $tipo = $licencia->tipo_licencia;
        $spec = self::especificaciones()[$tipo] ?? null;

        if (!$spec) {
            return [
                'titulo' => $licencia->nombre_licencia,
                'nombre' => $licencia->nombre_licencia,
                'precio' => (float) $licencia->precio_licencia,
                'formato' => $licencia->formato_audio ?? 'No especificado',
                'resumen' => $licencia->descripcion_licencia ?? 'Licencia personalizada.',
                'derechos' => $licencia->descripcion_licencia ?? 'Condiciones no especificadas.',
            ];
        }

        return $spec;
    }

    public static function precio(?Licencia $licencia): float
    {
        if ($licencia && $licencia->precio_licencia !== null) {
            return (float) $licencia->precio_licencia;
        }

        return (float) self::spec($licencia)['precio'];
    }

    public static function formato(?Licencia $licencia): string
    {
        return self::spec($licencia)['formato'];
    }

    public static function exclusivaVendida(string $tipoProducto, int $idProducto): bool
    {
        return CompraDetalle::where('tipo_producto', $tipoProducto)
            ->where('id_producto', $idProducto)
            ->whereHas('licencia', fn ($query) => $query->where('tipo_licencia', 'exclusiva'))
            ->exists();
    }
}
