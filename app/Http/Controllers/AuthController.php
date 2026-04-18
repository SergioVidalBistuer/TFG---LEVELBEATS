<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 * 
 * Controlador encargado de gestionar la autenticación de usuarios,
 * incluyendo el inicio de sesión, registro y cierre de sesión.
 */
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Maneja una solicitud de inicio de sesión.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con las credenciales.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function login(Request $request)
    {
        $request->validate([
            'direccion_correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $usuario = Usuario::where('direccion_correo', $request->direccion_correo)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()
                ->withErrors(['login' => 'Credenciales incorrectas'])
                ->withInput();
        }

        // Guardamos sesión completa
        session([
            'usuario_id' => $usuario->id,
            'usuario_nombre' => $usuario->nombre_usuario,
            'rol' => $usuario->rol, // IMPORTANTE para admin
        ]);

        return redirect()->route('beat.index')
            ->with('status', 'Sesión iniciada correctamente');
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRO
    |--------------------------------------------------------------------------
    */

    /**
     * Muestra el formulario de registro de usuario.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Maneja una solicitud de registro de nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos de registro.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo',
            'contrasena' => 'required|min:6',
        ]);

        Usuario::create([
            'nombre_usuario' => $request->nombre_usuario,
            'direccion_correo' => $request->direccion_correo,
            'contrasena' => Hash::make($request->contrasena),
            'rol' => 'usuario',
            'verificacion_completada' => false,
            'descripcion_perfil' => $request->descripcion_perfil,
            'calle' => $request->calle,
            'localidad' => $request->localidad,
            'provincia' => $request->provincia,
            'pais' => $request->pais,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('login')
            ->with('status', 'Cuenta creada correctamente');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    /**
     * Cierra la sesión del usuario actual.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->forget(['usuario_id', 'usuario_nombre', 'rol']);
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Sesión cerrada correctamente');
    }
}
