<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

/**
 * Class UsuarioController
 *
 * Controlador para la gestión de usuarios, incluyendo la visualización de perfiles,
 * creación, edición y eliminación de usuarios.
 */
class UsuarioController extends Controller
{
    // PERFIL DEL USUARIO LOGUEADO
    /**
     * Muestra el perfil del usuario logueado.
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el usuario en sesión no existe.
     */
    public function profile()
    {
        $usuario = Usuario::with(['beats', 'colecciones.beats', 'comprasComoComprador'])
            ->findOrFail(session('usuario_id'));

        return view('usuario.profile', compact('usuario'));
    }

    // LISTADO (solo admin por middleware)
    /**
     * Muestra un listado de todos los usuarios (solo accesible para administradores).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mostrar los más nuevos primero
        $usuarios = Usuario::orderBy('id', 'desc')->get();

        return view('usuario.index', compact('usuarios'));
    }

    // FORMULARIO CREAR
    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('usuario.create');
    }

    // GUARDAR
    /**
     * Guarda un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos del usuario.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function save(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo',
            'contrasena' => 'required|min:6',
            'rol' => 'nullable|in:usuario,admin'
        ]);

        Usuario::create([
            'nombre_usuario' => $request->nombre_usuario,
            'direccion_correo' => $request->direccion_correo,
            'contrasena' => Hash::make($request->contrasena),
            'rol' => session('rol') === 'admin'
                ? $request->input('rol', 'usuario')
                : 'usuario',
            'verificacion_completada' => 1,
            'descripcion_perfil' => $request->descripcion_perfil,
            'calle' => $request->calle,
            'localidad' => $request->localidad,
            'provincia' => $request->provincia,
            'pais' => $request->pais,
            'codigo_postal' => $request->codigo_postal,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario creado correctamente');
    }

    // FORMULARIO EDITAR
    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param  int  $id  Identificador único del usuario.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el usuario no existe.
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);

        return view('usuario.edit', compact('usuario'));
    }

    // ACTUALIZAR
    /**
     * Actualiza la información de un usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el usuario no existe.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo,' . $request->id,
            'rol' => 'nullable|in:usuario,admin'
        ]);

        $usuario = Usuario::findOrFail($request->id);

        $usuario->nombre_usuario = $request->nombre_usuario;
        $usuario->direccion_correo = $request->direccion_correo;
        $usuario->descripcion_perfil = $request->descripcion_perfil;
        $usuario->calle = $request->calle;
        $usuario->localidad = $request->localidad;
        $usuario->provincia = $request->provincia;
        $usuario->pais = $request->pais;
        $usuario->codigo_postal = $request->codigo_postal;

        // SOLO ADMIN puede cambiar rol
        if (session('rol') === 'admin') {
            $usuario->rol = $request->input('rol', 'usuario');
        }

        $usuario->save();

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario actualizado correctamente');
    }

    // ELIMINAR
    /**
     * Elimina un usuario de la base de datos.
     *
     * @param  int  $id  Identificador único del usuario.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el usuario no existe.
     */
    public function delete($id)
    {
        $usuario = Usuario::findOrFail($id);

        // Protección: no puede borrarse a sí mismo
        if (session('usuario_id') == $usuario->id) {
            return back()->with('status', 'No puedes eliminar tu propio usuario');
        }

        $usuario->delete();

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario eliminado correctamente');
    }
}
