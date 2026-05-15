<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware que restringe rutas al rol admin activo.
 */
class AdminOnly
{
    /**
     * Verifica autenticación y rol administrador antes de continuar la petición.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar sesión activa con el guard estándar de Laravel.
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('status', 'Debes iniciar sesión');
        }

        // Verificar el rol 'admin' usando el sistema N:N real (usuario_rol).
        // tieneRol() consulta la BD directamente: no depende de sesión manual.
        // Comprobación: usuario_rol.rol_activo = 1 AND rol.nombre_rol = 'admin'
        if (!auth()->user()->tieneRol('admin')) {
            abort(403, 'Acceso restringido a administradores.');
        }

        return $next($request);
    }
}
