<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;

class UsuarioEncargoController extends Controller
{
    /**
     * Asegura que el proyecto que se intenta ver pertenezca al cliente actual.
     */
    private function canView(Proyecto $proyecto): bool
    {
        $usuario = auth()->user();
        if ($usuario->esAdmin()) return true;

        return $proyecto->id_usuario === $usuario->id;
    }

    public function index()
    {
        // Traemos todos los proyectos encargados por el usuario logueado (el cliente)
        // Precargamos la relación de servicio y el ingeniero asociado al servicio
        $proyectos = Proyecto::with(['servicio.usuario'])
            ->where('id_usuario', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return view('usuario.encargos.index', compact('proyectos'));
    }

    public function detail($id)
    {
        $proyecto = Proyecto::with(['servicio.usuario', 'mensajes.emisor'])->findOrFail($id);
        
        if (!$this->canView($proyecto)) {
            abort(403, 'Acceso denegado a este encargo.');
        }

        $archivos = [];
        if ($proyecto->ruta_carpeta_archivos) {
            $archivos = \Illuminate\Support\Facades\Storage::disk('local')->files($proyecto->ruta_carpeta_archivos);
        }

        return view('usuario.encargos.detail', compact('proyecto', 'archivos'));
    }
}
