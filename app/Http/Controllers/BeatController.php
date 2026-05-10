<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;
use App\Models\Guardado;
use App\Support\LicenciaCompra;

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

    public function index(Request $request)
    {
        $query = Beat::with('usuario')->publicados();

        $query
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('titulo_beat', 'like', '%' . $request->input('q') . '%');
            })
            ->when($request->filled('genero'), function ($q) use ($request) {
                $q->where('genero_musical', $request->input('genero'));
            })
            ->when($request->filled('bpm_min'), function ($q) use ($request) {
                $q->where('tempo_bpm', '>=', $request->input('bpm_min'));
            })
            ->when($request->filled('bpm_max'), function ($q) use ($request) {
                $q->where('tempo_bpm', '<=', $request->input('bpm_max'));
            })
            ->when($request->filled('tono'), function ($q) use ($request) {
                $q->where('tono_musical', $request->input('tono'));
            })
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado_de_animo', $request->input('estado'));
            })
            ->when($request->filled('precio_min'), function ($q) use ($request) {
                $q->where('precio_base_licencia', '>=', $request->input('precio_min'));
            })
            ->when($request->filled('precio_max'), function ($q) use ($request) {
                $q->where('precio_base_licencia', '<=', $request->input('precio_max'));
            });

        $beats = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $opcionesFiltro = [
            'generos' => Beat::publicados()->whereNotNull('genero_musical')->where('genero_musical', '!=', '')->distinct()->orderBy('genero_musical')->pluck('genero_musical'),
            'tonos' => Beat::publicados()->whereNotNull('tono_musical')->where('tono_musical', '!=', '')->distinct()->orderBy('tono_musical')->pluck('tono_musical'),
            'estados' => Beat::publicados()->whereNotNull('estado_de_animo')->where('estado_de_animo', '!=', '')->distinct()->orderBy('estado_de_animo')->pluck('estado_de_animo'),
        ];

        // IDs de beats que el usuario autenticado ya tiene guardados
        $guardadosIds = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'beat')
                ->pluck('guardable_id')
                ->toArray()
            : [];

        return view('beat.index', compact('beats', 'opcionesFiltro', 'guardadosIds'));
    }

    public function detail($id)
    {
        $beat = Beat::with('colecciones')->findOrFail($id);
        if (!$beat->activo_publicado && !$this->canManage($beat)) {
            abort(404);
        }

        $licenciasCompra = LicenciaCompra::opciones();
        $exclusivaVendida = LicenciaCompra::exclusivaVendida('beat', $beat->id);

        $estaGuardado = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'beat')
                ->where('guardable_id', $beat->id)
                ->exists()
            : false;

        return view('beat.detail', compact('beat', 'licenciasCompra', 'exclusivaVendida', 'estaGuardado'));
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
