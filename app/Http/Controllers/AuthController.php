<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    private function sincronizarSesionUsuario(Usuario $usuario): void
    {
        session([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => $usuario->nombre_usuario,
        ]);
    }

    private function asignarRolUsuarioPorDefecto(Usuario $usuario): void
    {
        $rolPorDefecto = Rol::where('nombre_rol', 'usuario')->first();

        if ($rolPorDefecto && !$usuario->tieneRol('usuario')) {
            $usuario->roles()->attach($rolPorDefecto->id, ['rol_activo' => 1]);
        }
    }

    private function nombreUsuarioGoogle(string $nombre, string $email): string
    {
        $base = trim($nombre) !== '' ? trim($nombre) : Str::before($email, '@');
        $base = Str::limit(preg_replace('/\s+/', ' ', $base), 72, '');
        $nombreUsuario = $base;
        $contador = 2;

        while (Usuario::where('nombre_usuario', $nombreUsuario)->exists()) {
            $sufijo = ' ' . $contador;
            $nombreUsuario = Str::limit($base, 80 - strlen($sufijo), '') . $sufijo;
            $contador++;
        }

        return $nombreUsuario;
    }

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

        $usuario = Auth::user();
        $this->sincronizarSesionUsuario($usuario);

        return redirect()->intended(route('home.index'))
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

        $this->asignarRolUsuarioPorDefecto($usuario);

        $this->sincronizarSesionUsuario($usuario);

        return redirect()->route('home.index')
            ->with('status', 'Cuenta creada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | GOOGLE OAUTH
    |--------------------------------------------------------------------------
    */

    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            return redirect()->route('login')
                ->withErrors(['login' => 'No se pudo completar el acceso con Google. Inténtalo de nuevo.']);
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['login' => 'Google no devolvió un correo electrónico válido.']);
        }

        $usuario = Usuario::where('direccion_correo', $email)->first();
        $usuarioNuevo = false;
        $datosGoogle = [];

        if (Schema::hasColumn('usuario', 'google_id')) {
            $datosGoogle['google_id'] = $googleUser->getId();
        }

        if ($usuario) {
            if (!empty($datosGoogle) && empty($usuario->google_id)) {
                $usuario->forceFill($datosGoogle)->save();
            }
        } else {
            $usuarioNuevo = true;
            $usuario = Usuario::create(array_merge([
                'nombre_usuario'          => $this->nombreUsuarioGoogle($googleUser->getName() ?? '', $email),
                'direccion_correo'        => $email,
                'contrasena'              => Hash::make(Str::random(40)),
                'verificacion_completada' => true,
                'fecha_registro'          => now(),
            ], $datosGoogle));

            $this->asignarRolUsuarioPorDefecto($usuario);
        }

        Auth::login($usuario);
        $request->session()->regenerate();
        $this->sincronizarSesionUsuario($usuario);

        return redirect()
            ->route('home.index')
            ->with('status', $usuarioNuevo ? 'Cuenta creada con Google.' : 'Sesión iniciada con Google.');
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
