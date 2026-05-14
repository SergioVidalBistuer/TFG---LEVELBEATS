<?php

namespace App\Support;

class Imagenes
{
    private const PORTADAS_DIR = 'media/img/imagenesUsoLibreLevelBeats/portadas/';

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
