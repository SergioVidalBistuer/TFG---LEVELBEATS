<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Auditoria;

class StudioProyectoController extends Controller
{
    /**
     * Asegura que el proyecto a gestionar pertenezca a un servicio ofertado por el usuario logueado.
     */
    private function canManage(Proyecto $proyecto): bool
    {
        $usuario = auth()->user();
        if ($usuario->esAdmin()) return true;

        // Validamos que el servicio atado al proyecto pertenezca al técnico actual
        if ($proyecto->servicio) {
            return $proyecto->servicio->id_usuario === $usuario->id;
        }

        return false;
    }

    public function index()
    {
        abort_unless(auth()->user()->tieneRol('ingeniero') || auth()->user()->esAdmin(), 403, 'Acceso exclusivo para Ingenieros');

        // Traemos todos los proyectos cuyo SERVICIO haya sido creado por el usuario actual
        // Precargamos la relación cliente (quien nos contrató) y servicio (qué pacto compró)
        $proyectos = Proyecto::with(['cliente', 'servicio'])
            ->whereHas('servicio', function ($query) {
                $query->where('id_usuario', auth()->id());
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('studio.proyectos.index', compact('proyectos'));
    }

    public function edit($id)
    {
        $proyecto = Proyecto::with(['cliente', 'servicio'])->findOrFail($id);
        
        if (!$this->canManage($proyecto)) {
            abort(403, 'Acceso denegado a este proyecto técnico.');
        }

        return view('studio.proyectos.edit', compact('proyecto'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'estado_proyecto' => 'required|string|max:80',
            'notas_proyecto' => 'nullable|string|max:1000',
        ]);

        $proyecto = Proyecto::findOrFail($request->id);

        if (!$this->canManage($proyecto)) {
            abort(403);
        }

        $proyecto->update([
            'estado_proyecto' => $request->estado_proyecto,
            'notas_proyecto' => $request->notas_proyecto,
        ]);

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'actualizar',
            'entidad' => 'proyecto',
            'id_entidad' => $proyecto->id,
            'fecha' => now(),
        ]);

        return redirect()->route('studio.proyectos.index')->with('status', 'Progreso del proyecto actualizado correctamente.');
    }
}
