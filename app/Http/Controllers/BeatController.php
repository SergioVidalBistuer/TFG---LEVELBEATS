<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;

class BeatController extends Controller
{
    private function isAdmin(): bool
    {
        // Usa el sistema de roles real en lugar del ID hardcodeado
        return auth()->check() && auth()->user()->esAdmin();
    }

    private function canManage(Beat $beat): bool
    {
        return $this->isAdmin() || $beat->id_usuario === auth()->id();
    }

    public function index()
    {
        $beats = Beat::orderBy('id', 'desc')->paginate(15);
        return view('beat.index', compact('beats'));
    }

    public function detail($id)
    {
        $beat = Beat::with('colecciones')->findOrFail($id);
        return view('beat.detail', compact('beat'));
    }

    public function create()
    {
        return view('beat.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'titulo_beat' => 'required|string|max:140',
            'genero_musical' => 'nullable|string|max:80',
            'tempo_bpm' => 'nullable|integer',
            'tono_musical' => 'nullable|in:C,C#,D,D#,E,F,F#,G,G#,A,A#,B',
            'estado_de_animo' => 'nullable|string|max:80',
            'precio_base_licencia' => 'nullable|numeric',
            'url_audio_previsualizacion' => 'nullable|string|max:255',
            'url_archivo_final' => 'nullable|string|max:255',
            'url_portada_beat' => 'nullable|string|max:255',
            'activo_publicado' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        Beat::create([
            'id_usuario' => auth()->id(), // propietario = usuario autenticado
            'titulo_beat' => $request->input('titulo_beat'),
            'genero_musical' => $request->input('genero_musical'),
            'tempo_bpm' => $request->input('tempo_bpm'),
            'tono_musical' => $request->input('tono_musical'),
            'estado_de_animo' => $request->input('estado_de_animo'),
            'precio_base_licencia' => $request->input('precio_base_licencia', 0),
            'url_audio_previsualizacion' => $request->input('url_audio_previsualizacion'),
            'url_archivo_final' => $request->input('url_archivo_final'),
            'url_portada_beat' => $request->input('url_portada_beat'),
            'activo_publicado' => (bool)$request->input('activo_publicado', false),
            'fecha_publicacion' => $request->input('fecha_publicacion'),
        ]);

        return redirect()->action([BeatController::class, 'index'])
            ->with('status', 'Beat creado correctamente');
    }

    public function edit($id)
    {
        $beat = Beat::findOrFail($id);

        if (!$this->canManage($beat)) {
            abort(403);
        }

        return view('beat.create', compact('beat'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:beat,id',
            'titulo_beat' => 'required|string|max:140',
            'genero_musical' => 'nullable|string|max:80',
            'tempo_bpm' => 'nullable|integer',
            'tono_musical' => 'nullable|in:C,C#,D,D#,E,F,F#,G,G#,A,A#,B',
            'estado_de_animo' => 'nullable|string|max:80',
            'precio_base_licencia' => 'nullable|numeric',
            'url_audio_previsualizacion' => 'nullable|string|max:255',
            'url_archivo_final' => 'nullable|string|max:255',
            'url_portada_beat' => 'nullable|string|max:255',
            'activo_publicado' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        $beat = Beat::findOrFail($request->input('id'));

        if (!$this->canManage($beat)) {
            abort(403);
        }

        $beat->update([
            // id_usuario NO se cambia (ni por admin, salvo que lo quieras explícito)
            'titulo_beat' => $request->input('titulo_beat'),
            'genero_musical' => $request->input('genero_musical'),
            'tempo_bpm' => $request->input('tempo_bpm'),
            'tono_musical' => $request->input('tono_musical'),
            'estado_de_animo' => $request->input('estado_de_animo'),
            'precio_base_licencia' => $request->input('precio_base_licencia', 0),
            'url_audio_previsualizacion' => $request->input('url_audio_previsualizacion'),
            'url_archivo_final' => $request->input('url_archivo_final'),
            'url_portada_beat' => $request->input('url_portada_beat'),
            'activo_publicado' => (bool)$request->input('activo_publicado', false),
            'fecha_publicacion' => $request->input('fecha_publicacion'),
        ]);

        return redirect()->action([BeatController::class, 'index'])
            ->with('status', 'Beat actualizado correctamente');
    }

    public function delete($id)
    {
        $beat = Beat::findOrFail($id);

        if (!$this->canManage($beat)) {
            abort(403);
        }

        $beat->delete();

        return redirect()->action([BeatController::class, 'index'])
            ->with('status', 'Beat borrado correctamente');
    }
}
