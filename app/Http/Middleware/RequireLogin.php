<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLogin
{
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
