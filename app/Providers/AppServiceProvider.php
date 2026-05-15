<?php

namespace App\Providers;

use App\Models\MensajeDirecto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Proveedor principal de servicios de la aplicación.
 *
 * Registra configuración transversal como el morph map de guardados y variables
 * compartidas del layout principal.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra bindings de contenedor propios de la aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa relaciones polimórficas y datos compartidos para vistas comunes.
     */
    public function boot(): void
    {
        /*
        |----------------------------------------------------------------------
        | MORPH MAP — Guardados polimórficos
        |----------------------------------------------------------------------
        | Sin este mapa, Laravel intentaría resolver los strings 'beat',
        | 'coleccion' y 'servicio' como nombres de clase PHP literales y
        | lanzaría "Class 'beat' not found".
        |
        | El valor guardado en BD (guardable_type) sigue siendo el string corto:
        |   beat | coleccion | servicio
        |----------------------------------------------------------------------
        */
        Relation::morphMap([
            'beat'      => \App\Models\Beat::class,
            'coleccion' => \App\Models\Coleccion::class,
            'servicio'  => \App\Models\Servicio::class,
        ]);

        View::composer('layouts.master', function ($view) {
            $mensajesNoLeidos = 0;

            if (auth()->check() && Schema::hasTable('conversacion') && Schema::hasTable('mensaje_directo')) {
                $usuarioId = auth()->id();

                $mensajesNoLeidos = MensajeDirecto::query()
                    ->where('emisor_id', '<>', $usuarioId)
                    ->where('leido', false)
                    ->whereHas('conversacion', function ($query) use ($usuarioId) {
                        $query->where('usuario_uno_id', $usuarioId)
                            ->orWhere('usuario_dos_id', $usuarioId);
                    })
                    ->count();
            }

            $view->with([
                'mensajesNoLeidos' => $mensajesNoLeidos,
                'mensajesNoLeidosLabel' => $mensajesNoLeidos >= 100 ? '+99' : $mensajesNoLeidos,
            ]);
        });
    }
}
