<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware de acceso autenticado.
 *
 * Protege las rutas privadas de LevelBeats usando el guard estándar de Laravel.
 */
class RequireLogin
{
    /**
     * Redirige a login si no existe sesión autenticada.
     */
    public function handle(Request $request, Closure $next)
    {
        // Usa el guard estándar de Laravel en lugar de session() manual.
        // auth()->check() verifica la sesión de Auth (via Auth::attempt/login).
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('status', 'Debes iniciar sesión');
        }

        return $next($request);
    }
}
