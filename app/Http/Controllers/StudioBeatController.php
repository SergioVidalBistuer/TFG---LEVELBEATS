<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    private function guardarArchivoAudio(Request $request, string $campo = 'archivo_audio'): ?string
    {
        if (!$request->hasFile($campo)) {
            return null;
        }

        $archivo = $request->file($campo);
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('beats/audio/' . auth()->id(), $nombre, 'public');

        return 'storage/' . $ruta;
    }

    private function guardarImagenPortada(Request $request, string $campo = 'portada_beat'): ?string
    {
        if (!$request->hasFile($campo)) {
            return null;
        }

        $archivo = $request->file($campo);
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('beats/covers/' . auth()->id(), $nombre, 'public');

        return 'storage/' . $ruta;
    }

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if (!$ruta || !str_starts_with($ruta, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($ruta, strlen('storage/')));
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
            'archivo_audio'         => 'required|file|mimes:mp3,wav,aiff,aif,flac,m4a|max:102400',
            'portada_beat'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'archivo_audio.required' => 'Selecciona el archivo de audio principal del beat.',
            'archivo_audio.file' => 'El archivo de audio no es válido.',
            'archivo_audio.mimes' => 'El archivo debe estar en formato MP3, WAV, AIFF, AIF, FLAC o M4A.',
            'archivo_audio.max' => 'El archivo de audio no puede superar los 100 MB.',
            'portada_beat.image' => 'La portada debe ser una imagen válida.',
            'portada_beat.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_beat.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $rutaAudio = $this->guardarArchivoAudio($request);
        $rutaPortada = $this->guardarImagenPortada($request);

        Beat::create([
            'id_usuario'           => auth()->id(),
            'titulo_beat'          => $request->titulo_beat,
            'genero_musical'       => $request->genero_musical,
            'tempo_bpm'            => $request->tempo_bpm,
            'tono_musical'         => $request->tono_musical,
            'precio_base_licencia' => $request->precio_base_licencia,
            'url_audio_previsualizacion' => $rutaAudio,
            'url_archivo_final' => $rutaAudio,
            'url_portada_beat' => $rutaPortada,
            'activo_publicado'     => $request->has('activo_publicado'),
            'fecha_publicacion'    => now(),
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
            'archivo_audio'         => 'nullable|file|mimes:mp3,wav,aiff,aif,flac,m4a|max:102400',
            'portada_beat'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'archivo_audio.file' => 'El archivo de audio no es válido.',
            'archivo_audio.mimes' => 'El archivo debe estar en formato MP3, WAV, AIFF, AIF, FLAC o M4A.',
            'archivo_audio.max' => 'El archivo de audio no puede superar los 100 MB.',
            'portada_beat.image' => 'La portada debe ser una imagen válida.',
            'portada_beat.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_beat.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $beat = Beat::findOrFail($request->id);
        if (!$this->canManage($beat)) {
            abort(403);
        }

        $datos = [
            'titulo_beat'          => $request->titulo_beat,
            'genero_musical'       => $request->genero_musical,
            'tempo_bpm'            => $request->tempo_bpm,
            'tono_musical'         => $request->tono_musical,
            'precio_base_licencia' => $request->precio_base_licencia,
            'activo_publicado'     => $request->has('activo_publicado'),
        ];

        if ($request->hasFile('archivo_audio')) {
            $rutaAudio = $this->guardarArchivoAudio($request);
            $this->eliminarArchivoPublico($beat->url_audio_previsualizacion);

            if ($beat->url_archivo_final !== $beat->url_audio_previsualizacion) {
                $this->eliminarArchivoPublico($beat->url_archivo_final);
            }

            $datos['url_audio_previsualizacion'] = $rutaAudio;
            $datos['url_archivo_final'] = $rutaAudio;
        }

        if ($request->hasFile('portada_beat')) {
            $rutaPortada = $this->guardarImagenPortada($request);
            $this->eliminarArchivoPublico($beat->url_portada_beat);
            $datos['url_portada_beat'] = $rutaPortada;
        }

        $beat->update($datos);

        return redirect()->route('studio.beats.index')->with('status', 'Beat actualizado con éxito.');
    }

    public function delete($id)
    {
        $beat = Beat::findOrFail($id);
        if (!$this->canManage($beat)) {
            abort(403);
        }

        $beat->delete();

        return redirect()->route('studio.beats.index')->with('status', 'Beat retirado del catálogo.');
    }
}
