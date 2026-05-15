<?php

namespace App\Support;

/**
 * Helper de resolución de imágenes públicas.
 *
 * Permite que la base de datos conserve rutas antiguas jpg/png de portadas y
 * que las vistas sirvan automáticamente el equivalente webp cuando existe.
 */
class Imagenes
{
    private const PORTADAS_DIR = 'media/img/imagenesUsoLibreLevelBeats/portadas/';

    /**
     * Devuelve una URL pública para una portada, respetando URLs externas y fallback.
     */
    public static function portada(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        $relative = ltrim($path, '/');

        if (str_starts_with($relative, self::PORTADAS_DIR)) {
            $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $relative);

            if ($webp !== $relative) {
                $webpPath = public_path($webp);
                $originalPath = public_path($relative);

                if (is_file($webpPath) || !is_file($originalPath)) {
                    $relative = $webp;
                }
            }
        }

        return asset($relative);
    }
}
