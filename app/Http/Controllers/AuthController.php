<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'direccion_correo' => 'required|email',
            'contrasena'       => 'required|string',
        ]);

        // Auth::attempt() requiere estrictamente que la clave del array sea 'password'
        // para que sepa qué campo tiene que hashear, aunque en la BD se llame 'contrasena'.
        $credentials = [
            'direccion_correo' => $request->direccion_correo,
            'password'         => $request->contrasena,
        ];

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['login' => 'Credenciales incorrectas'])
                ->withInput();
        }

        $request->session()->regenerate();

        // ⚠️ BRIDGE DE COMPATIBILIDAD (temporal hasta completar la migración)
        // Los controladores aún usan session('usuario_id') y session('usuario_nombre').
        // Se mantienen para no romper BeatController, CompraController, etc.
        // Se eliminarán en la fase final cuando todos usen auth()->user().
        $usuario = Auth::user();
        session([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => $usuario->nombre_usuario,
            // NOTA: 'rol' se omite intencionalmente.
            // La columna ya no existe en la tabla 'usuario'.
            // Los controladores/vistas que comprueban session('rol') === 'admin'
            // verán null → las funciones admin quedan desactivadas hasta la Fase 2.
        ]);

        return redirect()->route('beat.index')
            ->with('status', 'Sesión iniciada correctamente');
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRO
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre_usuario'   => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo',
            'contrasena'       => 'required|min:6',
        ]);

        $usuario = Usuario::create([
            'nombre_usuario'          => $request->nombre_usuario,
            'direccion_correo'        => $request->direccion_correo,
            'contrasena'              => Hash::make($request->contrasena),
            // 'rol' eliminado: ya no es columna de 'usuario'.
            // La asignación de roles se hará en usuario_rol en la Fase 2.
            'verificacion_completada' => false,
            'descripcion_perfil'      => $request->descripcion_perfil,
            'calle'                   => $request->calle,
            'localidad'               => $request->localidad,
            'provincia'               => $request->provincia,
            'pais'                    => $request->pais,
            'fecha_registro'          => now(),
        ]);

        // Loguear al usuario recién creado
        Auth::login($usuario);
        $request->session()->regenerate();

        // Asignar rol por defecto 'usuario' en la tabla pivote usuario_rol.
        // Se usa una guard de seguridad por si el seeder de roles aún no se ha ejecutado.
        $rolPorDefecto = Rol::where('nombre_rol', 'usuario')->first();
        if ($rolPorDefecto) {
            $usuario->roles()->attach($rolPorDefecto->id, ['rol_activo' => 1]);
        }

        // ⚠️ BRIDGE DE COMPATIBILIDAD (mismo motivo que en login())
        session([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => $usuario->nombre_usuario,
        ]);

        return redirect()->route('onboarding.roles')
            ->with('status', '¡Cuenta creada! Primer paso: define tu vía.');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalida toda la sesión (incluye usuario_id, usuario_nombre y cart)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Sesión cerrada correctamente');
    }
}
