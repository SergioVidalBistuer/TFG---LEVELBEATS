<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class RequireLogin
 *
 * Middleware encargado de verificar que el usuario ha iniciado sesión.
 */
class RequireLogin
{
    /**
     * Maneja la solicitud entrante asegurando que el usuario está logueado.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP actual.
     * @param  \Closure  $next  La siguiente operación de middleware/controlador.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login')
                ->with('status', 'Debes iniciar sesión');
        }

        return $next($request);
    }
}
