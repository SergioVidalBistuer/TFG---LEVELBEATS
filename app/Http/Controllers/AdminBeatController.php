<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controlador administrativo de beats.
 *
 * Permite al administrador revisar y editar beats de cualquier productor sin
 * depender de permisos de propietario.
 */
class AdminBeatController extends Controller
{
    private function guardarArchivoAudio(Request $request, Beat $beat, string $campo = 'archivo_audio'): ?string
    {
        if (!$request->hasFile($campo)) {
            return null;
        }

        $archivo = $request->file($campo);
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('beats/audio/' . $beat->id_usuario, $nombre, 'public');

        return 'storage/' . $ruta;
    }

    private function guardarImagenPortada(Request $request, Beat $beat, string $campo = 'portada_beat'): ?string
    {
        if (!$request->hasFile($campo)) {
            return null;
        }

        $archivo = $request->file($campo);
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('beats/covers/' . $beat->id_usuario, $nombre, 'public');

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
        $beats = Beat::with('usuario')->orderBy('id', 'desc')->get();

        return view('admin.beats.index', compact('beats'));
    }

    public function edit($id)
    {
        $beat = Beat::with('usuario')->findOrFail($id);

        return view('admin.beats.form', compact('beat'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                   => 'required|integer|exists:beat,id',
            'titulo_beat'          => 'required|string|max:140',
            'genero_musical'       => 'nullable|string|max:80',
            'estado_de_animo'      => 'nullable|string|max:80',
            'tempo_bpm'            => 'nullable|integer',
            'tono_musical'         => 'nullable|in:C,C#,D,D#,E,F,F#,G,G#,A,A#,B',
            'precio_base_licencia' => 'required|numeric',
            'archivo_audio'        => 'nullable|file|mimes:mp3,wav,aiff,aif,flac,m4a|max:102400',
            'portada_beat'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'archivo_audio.file' => 'El archivo de audio no es válido.',
            'archivo_audio.mimes' => 'El archivo debe estar en formato MP3, WAV, AIFF, AIF, FLAC o M4A.',
            'archivo_audio.max' => 'El archivo de audio no puede superar los 100 MB.',
            'portada_beat.image' => 'La portada debe ser una imagen válida.',
            'portada_beat.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_beat.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $beat = Beat::findOrFail($request->input('id'));

        $datos = [
            'titulo_beat'          => $request->input('titulo_beat'),
            'genero_musical'       => $request->input('genero_musical'),
            'estado_de_animo'      => $request->input('estado_de_animo'),
            'tempo_bpm'            => $request->input('tempo_bpm'),
            'tono_musical'         => $request->input('tono_musical'),
            'precio_base_licencia' => $request->input('precio_base_licencia'),
            'activo_publicado'     => $request->has('activo_publicado'),
        ];

        if ($request->hasFile('archivo_audio')) {
            $rutaAudio = $this->guardarArchivoAudio($request, $beat);
            $this->eliminarArchivoPublico($beat->url_audio_previsualizacion);

            if ($beat->url_archivo_final !== $beat->url_audio_previsualizacion) {
                $this->eliminarArchivoPublico($beat->url_archivo_final);
            }

            $datos['url_audio_previsualizacion'] = $rutaAudio;
            $datos['url_archivo_final'] = $rutaAudio;
        }

        if ($request->hasFile('portada_beat')) {
            $rutaPortada = $this->guardarImagenPortada($request, $beat);
            $this->eliminarArchivoPublico($beat->url_portada_beat);
            $datos['url_portada_beat'] = $rutaPortada;
        }

        $beat->update($datos);

        return redirect()->route('admin.beats.index')->with('status', 'Beat actualizado correctamente.');
    }
}
