<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PerfilController extends Controller
{
    private function perfilPublicoDisponible(): bool
    {
        return Schema::hasColumn('usuario', 'perfil_publico');
    }

    public function index(Request $request)
    {
        $perfilPublicoDisponible = $this->perfilPublicoDisponible();
        $rol = $request->query('rol');
        $busqueda = trim((string) $request->query('q', ''));

        $perfiles = collect();

        if ($perfilPublicoDisponible) {
            $query = Usuario::query()
                ->with(['roles' => fn($q) => $q->where('usuario_rol.rol_activo', 1)])
                ->withCount([
                    'beats as beats_publicados_count' => fn($q) => $q->where('activo_publicado', true),
                    'servicios as servicios_activos_count' => fn($q) => $q->where('servicio_activo', true),
                ])
                ->where('perfil_publico', true)
                ->whereHas('roles', function ($q) use ($rol) {
                    $q->where('usuario_rol.rol_activo', 1)
                      ->whereIn('nombre_rol', ['productor', 'ingeniero']);

                    if (in_array($rol, ['productor', 'ingeniero'], true)) {
                        $q->where('nombre_rol', $rol);
                    }
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('usuario_rol.rol_activo', 1)->where('nombre_rol', 'admin');
                });

            if ($busqueda !== '') {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre_usuario', 'like', '%' . $busqueda . '%')
                      ->orWhere('descripcion_perfil', 'like', '%' . $busqueda . '%')
                      ->orWhere('localidad', 'like', '%' . $busqueda . '%');
                });
            }

            $perfiles = $query->orderBy('nombre_usuario')->get();
        }

        return view('perfiles.index', compact('perfiles', 'rol', 'busqueda', 'perfilPublicoDisponible'));
    }

    public function show(Usuario $usuario)
    {
        abort_unless($this->perfilPublicoDisponible() && $usuario->perfil_publico, 404);

        $usuario->load(['roles' => fn($q) => $q->where('usuario_rol.rol_activo', 1)]);

        abort_if($usuario->esAdmin(), 404);
        abort_unless($usuario->tieneRol('productor') || $usuario->tieneRol('ingeniero'), 404);

        $beats = $usuario->beats()->where('activo_publicado', true)->orderBy('id', 'desc')->take(6)->get();
        $colecciones = $usuario->colecciones()->where('activo_publicado', true)->with('beats')->orderBy('id', 'desc')->take(6)->get();
        $servicios = $usuario->servicios()->where('servicio_activo', true)->orderBy('id', 'desc')->take(6)->get();

        return view('perfiles.show', compact('usuario', 'beats', 'colecciones', 'servicios'));
    }
}
