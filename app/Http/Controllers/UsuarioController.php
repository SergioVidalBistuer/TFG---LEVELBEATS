<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // PERFIL DEL USUARIO LOGUEADO
    public function profile()
    {
        // Eager-load incluye 'roles' para que las vistas puedan mostrar badges sin N+1
        $usuario = Usuario::with(['beats', 'colecciones.beats', 'comprasComoComprador', 'roles'])
            ->findOrFail(auth()->id());

        // ALGORITMO SELF-HEALING: Limpieza de duplicados históricos en Backend
        $subsHistoricas = $usuario->suscripciones()
            ->with(['planPorRol.rol'])
            ->where('estado_suscripcion', 'activa')
            ->orderByDesc('id')
            ->get();
            
        $rolesAuditados = [];
        foreach ($subsHistoricas as $sub) {
            if ($sub->planPorRol && $sub->planPorRol->rol) {
                $rol_nombre = $sub->planPorRol->rol->nombre_rol;
                if (in_array($rol_nombre, $rolesAuditados)) {
                    // Ya vimos una sub activa más reciente. Silenciamos histórico duplicado.
                    $sub->update(['estado_suscripcion' => 'expirada']);
                } else {
                    $rolesAuditados[] = $rol_nombre;
                }
            }
        }

        return view('usuario.profile', compact('usuario'));
    }

    // LISTADO (solo admin por middleware)
    public function index()
    {
        // 'roles' eager-loaded para evitar N+1 al mostrar rol de cada usuario en tabla
        $usuarios = Usuario::with('roles')->orderBy('id', 'desc')->get();

        return view('usuario.index', compact('usuarios'));
    }

    // FORMULARIO CREAR
    public function create()
    {
        return view('usuario.create');
    }

    // GUARDAR
    public function save(Request $request)
    {
        $request->validate([
            'nombre_usuario'   => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo',
            'contrasena'       => 'required|min:6',
            'rol'              => 'nullable|in:usuario,admin', // nombre_rol válido en la tabla rol
        ]);

        $usuario = Usuario::create([
            'nombre_usuario'          => $request->nombre_usuario,
            'direccion_correo'        => $request->direccion_correo,
            'contrasena'              => Hash::make($request->contrasena),
            // 'rol' eliminado: se asigna vía tabla pivote usuario_rol, no como columna
            'verificacion_completada' => 1,
            'descripcion_perfil'      => $request->descripcion_perfil,
            'calle'                   => $request->calle,
            'localidad'               => $request->localidad,
            'provincia'               => $request->provincia,
            'pais'                    => $request->pais,
            'codigo_postal'           => $request->codigo_postal,
            'fecha_registro'          => now(),
        ]);

        // Admin puede asignar rol al crear; el resto recibe 'usuario' por defecto
        $rolNombre = auth()->user()->esAdmin()
            ? $request->input('rol', 'usuario')
            : 'usuario';

        $rol = Rol::where('nombre_rol', $rolNombre)->first();
        if ($rol) {
            $usuario->roles()->attach($rol->id, ['rol_activo' => 1]);
        }

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario creado correctamente');
    }

    // FORMULARIO EDITAR
    public function edit($id)
    {
        // Cargar roles para que edit.blade.php pueda detectar el rol base actual
        $usuario = Usuario::with('roles')->findOrFail($id);

        return view('usuario.edit', compact('usuario'));
    }

    // ACTUALIZAR
    public function update(Request $request)
    {
        $request->validate([
            'nombre_usuario'   => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo,' . $request->id,
            'rol'              => 'nullable|in:usuario,admin',
        ]);

        $usuario = Usuario::findOrFail($request->id);

        $usuario->nombre_usuario     = $request->nombre_usuario;
        $usuario->direccion_correo   = $request->direccion_correo;
        $usuario->descripcion_perfil = $request->descripcion_perfil;
        $usuario->calle              = $request->calle;
        $usuario->localidad          = $request->localidad;
        $usuario->provincia          = $request->provincia;
        $usuario->pais               = $request->pais;
        $usuario->codigo_postal      = $request->codigo_postal;

        $usuario->save();

        // SOLO ADMIN puede cambiar el rol base del usuario
        if (auth()->user()->esAdmin() && $request->filled('rol')) {
            $rolNuevo = Rol::where('nombre_rol', $request->input('rol'))->first();

            if ($rolNuevo) {
                // Detach SOLO los roles base (admin/usuario), preservando
                // roles especializados como productor e ingeniero
                $rolesBase = Rol::whereIn('nombre_rol', ['admin', 'usuario'])->pluck('id');
                $usuario->roles()->detach($rolesBase);
                $usuario->roles()->attach($rolNuevo->id, ['rol_activo' => 1]);
            }
        }

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario actualizado correctamente');
    }

    // ELIMINAR
    public function delete($id)
    {
        $usuario = Usuario::findOrFail($id);

        // Protección: no puede borrarse a sí mismo
        if (auth()->id() == $usuario->id) {
            return back()->with('status', 'No puedes eliminar tu propio usuario');
        }

        $usuario->delete();

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario eliminado correctamente');
    }
}

