<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AdminOnly
 *
 * Middleware encargado de verificar si el usuario autenticado tiene el rol de administrador.
 */
class AdminOnly
{
    /**
     * Maneja la solicitud entrante asegurando que el usuario es administrador.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP actual.
     * @param  \Closure  $next  La siguiente operación de middleware/controlador.
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function handle(Request $request, Closure $next)
    {
        //  Si no está logueado
        if (!session()->has('usuario_id')) {
            return redirect()->route('login')
                ->with('status', 'Debes iniciar sesión');
        }

        //  Si no es admin
        if (session('rol') !== 'admin') {
            abort(403); // acceso prohibido
        }

        return $next($request);
    }
}
