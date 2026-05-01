<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;
use App\Models\Auditoria;

class StudioBeatController extends Controller
{
    /**
     * Asegura que el productor solo modifique sus pistas o que sea admin root.
     */
    private function canManage(Beat $beat): bool
    {
        $usuario = auth()->user();
        return $beat->id_usuario === $usuario->id || $usuario->esAdmin();
    }

    public function index()
    {
        abort_unless(auth()->user()->tieneRol('productor') || auth()->user()->esAdmin(), 403, 'Acceso exclusivo para Productores');

        // El productor ve SOLO su propio catálogo (sus beats):
        $beats = Beat::where('id_usuario', auth()->id())->orderBy('id', 'desc')->get();
        return view('studio.beats.index', compact('beats'));
    }

    public function create()
    {
        abort_unless(auth()->user()->tieneRol('productor') || auth()->user()->esAdmin(), 403);
        
        $usuario = auth()->user();
        if (!$usuario->esAdmin()) {
            $rolProductor = \App\Models\Rol::where('nombre_rol', 'productor')->first();
            $sub = $usuario->suscripciones()->with('planPorRol')
                           ->where('id_rol', $rolProductor->id)
                           ->where('estado_suscripcion', 'activa')
                           ->latest('fecha_inicio')->first();
            
            if (!$sub) {
                return redirect()->route('onboarding.planes', ['rol' => 'productor'])
                                 ->with('status', 'Aviso: Necesitas suscribirte a un Plan para publicar Beats.');
            }

            $limite = $sub->planPorRol->beats_publicables_mes;
            $beatsSubidos = Beat::where('id_usuario', $usuario->id)->count();

            // Límite de seguridad básica (si límite < 90 asume que no es plan ilimitado)
            if ($beatsSubidos >= $limite && $limite < 90) {
                return redirect()->route('studio.beats.index')
                                 ->with('status', "Límite superado: Tu plan restringe a $limite beats. Actualiza tu suscripción.");
            }
        }

        return view('studio.beats.form');
    }

    public function save(Request $request)
    {
        // Doble validación de seguridad back-end para POST spoofing
        $usuario = auth()->user();
        if (!$usuario->esAdmin()) {
            $rolProductor = \App\Models\Rol::where('nombre_rol', 'productor')->first();
            $sub = $usuario->suscripciones()->with('planPorRol')
                           ->where('id_rol', $rolProductor->id)
                           ->where('estado_suscripcion', 'activa')
                           ->latest('fecha_inicio')->first();
            
            $limite = $sub ? $sub->planPorRol->beats_publicables_mes : 0;
            $beatsSubidos = Beat::where('id_usuario', $usuario->id)->count();

            if (!$sub || ($beatsSubidos >= $limite && $limite < 90)) {
                return redirect()->route('studio.beats.index')->with('status', "Plan insuficiente.");
            }
        }

        $request->validate([
            'titulo_beat'          => 'required|string|max:140',
            'genero_musical'       => 'nullable|string|max:80',
            'tempo_bpm'            => 'nullable|integer',
            'tono_musical'         => 'nullable|in:C,C#,D,D#,E,F,F#,G,G#,A,A#,B',
            'precio_base_licencia' => 'required|numeric',
        ]);

        $beat = Beat::create([
            'id_usuario'           => auth()->id(),
            'titulo_beat'          => $request->titulo_beat,
            'genero_musical'       => $request->genero_musical,
            'tempo_bpm'            => $request->tempo_bpm,
            'tono_musical'         => $request->tono_musical,
            'precio_base_licencia' => $request->precio_base_licencia,
            'activo_publicado'     => $request->has('activo_publicado'),
            'fecha_publicacion'    => now(),
        ]);

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'crear',
            'entidad' => 'beat',
            'id_entidad' => $beat->id,
            'fecha' => now(),
        ]);

        return redirect()->route('studio.beats.index')->with('status', 'Beat subido con éxito al inventario.');
    }

    public function edit($id)
    {
        $beat = Beat::findOrFail($id);
        if (!$this->canManage($beat)) {
            abort(403, 'Acceso denegado a este beat.');
        }

        return view('studio.beats.form', compact('beat'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                   => 'required|integer',
            'titulo_beat'          => 'required|string|max:140',
            'genero_musical'       => 'nullable|string|max:80',
            'tempo_bpm'            => 'nullable|integer',
            'tono_musical'         => 'nullable|in:C,C#,D,D#,E,F,F#,G,G#,A,A#,B',
            'precio_base_licencia' => 'required|numeric',
        ]);

        $beat = Beat::findOrFail($request->id);
        if (!$this->canManage($beat)) {
            abort(403);
        }

        $beat->update([
            'titulo_beat'          => $request->titulo_beat,
            'genero_musical'       => $request->genero_musical,
            'tempo_bpm'            => $request->tempo_bpm,
            'tono_musical'         => $request->tono_musical,
            'precio_base_licencia' => $request->precio_base_licencia,
            'activo_publicado'     => $request->has('activo_publicado'),
        ]);

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'actualizar',
            'entidad' => 'beat',
            'id_entidad' => $beat->id,
            'fecha' => now(),
        ]);

        return redirect()->route('studio.beats.index')->with('status', 'Beat actualizado con éxito.');
    }

    public function delete($id)
    {
        $beat = Beat::findOrFail($id);
        if (!$this->canManage($beat)) {
            abort(403);
        }

        $id_entidad = $beat->id;
        $beat->delete();

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'eliminar',
            'entidad' => 'beat',
            'id_entidad' => $id_entidad,
            'fecha' => now(),
        ]);
        return redirect()->route('studio.beats.index')->with('status', 'Beat retirado del catálogo.');
    }
}
