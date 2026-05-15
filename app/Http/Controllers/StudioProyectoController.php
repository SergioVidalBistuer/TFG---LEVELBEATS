<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Controlador de Studio para proyectos recibidos por ingenieros.
 *
 * Gestiona seguimiento, estados, archivos y aceptación/cancelación de encargos
 * asociados a servicios del usuario autenticado.
 */
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
        $proyecto = Proyecto::with(['cliente', 'servicio', 'mensajes.emisor', 'archivos.usuario'])->findOrFail($id);
        
        if (!$this->canManage($proyecto)) {
            abort(403, 'Acceso denegado a este proyecto técnico.');
        }

        $archivos = $proyecto->archivos
            ->sortByDesc(fn ($archivo) => optional($archivo->fecha_subida)->timestamp ?? $archivo->id)
            ->values();
        $archivosCliente = $archivos->filter(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) $proyecto->id_usuario)->values();
        $archivosIngeniero = $archivos->filter(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) ($proyecto->servicio->id_usuario ?? 0))->values();
        $archivosSinAutor = $archivos->reject(fn ($archivo) => (int) ($archivo->id_usuario ?? 0) === (int) $proyecto->id_usuario || (int) ($archivo->id_usuario ?? 0) === (int) ($proyecto->servicio->id_usuario ?? 0))->values();

        return view('studio.proyectos.edit', compact('proyecto', 'archivos', 'archivosCliente', 'archivosIngeniero', 'archivosSinAutor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'estado_proyecto' => ['required', Rule::in([
                'pendiente_archivos',
                'pendiente_aceptacion_ingeniero',
                'pendiente_pago_cliente',
                'archivos_recibidos',
                'en_proceso',
                'en_revision',
                'entregado',
                'cerrado',
                'cancelado',
            ])],
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

        return redirect()->route('studio.proyectos.index')->with('status', 'Progreso del proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->load(['servicio', 'archivos', 'mensajes']);

        if (!$this->canManage($proyecto)) {
            abort(403, 'Acceso denegado a este proyecto técnico.');
        }

        if (!in_array($proyecto->estado_proyecto, ['cancelado', 'cerrado'], true)) {
            return back()->with('status', 'Solo puedes eliminar encargos cancelados o cerrados.');
        }

        DB::transaction(function () use ($proyecto) {
            foreach ($proyecto->archivos as $archivo) {
                if ($archivo->archivo && Storage::disk('local')->exists($archivo->archivo)) {
                    Storage::disk('local')->delete($archivo->archivo);
                }

                $archivo->delete();
            }

            $proyecto->mensajes()->delete();
            $proyecto->delete();
        });

        return redirect()
            ->route('studio.proyectos.index')
            ->with('status', 'Encargo eliminado correctamente.');
    }
}
