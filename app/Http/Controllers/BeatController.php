<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;

/**
 * Class BeatController
 * 
 * Controlador para la gestión de beats, incluyendo su creación,
 * edición, borrado y visualización.
 */
class BeatController extends Controller
{
    /**
     * Verifica si el usuario actual tiene rol de administrador.
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        return session('usuario_id') === 1;
    }

    /**
     * Verifica si el usuario actual puede gestionar el beat indicado.
     *
     * @param  \App\Models\Beat  $beat
     * @return bool
     */
    private function canManage(Beat $beat): bool
    {
        return $this->isAdmin() || $beat->id_usuario === session('usuario_id');
    }

    /**
     * Muestra una lista paginada de todos los beats.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $beats = Beat::orderBy('id', 'desc')->paginate(15);
        return view('beat.index', compact('beats'));
    }

    /**
     * Muestra los detalles de un beat en específico.
     *
     * @param  int  $id  Identificador único del beat.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el beat no existe.
     */
    public function detail($id)
    {
        $beat = Beat::with('colecciones')->findOrFail($id);
        return view('beat.detail', compact('beat'));
    }

    /**
     * Muestra el formulario para crear un nuevo beat.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('beat.create');
    }

    /**
     * Guarda un nuevo beat en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos del beat.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
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
            'id_usuario' => session('usuario_id'),
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

    /**
     * Muestra el formulario para editar un beat existente.
     *
     * @param  int  $id  Identificador único del beat.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el beat no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function edit($id)
    {
        $beat = Beat::findOrFail($id);

        if (!$this->canManage($beat)) {
            abort(403);
        }

        return view('beat.create', compact('beat'));
    }

    /**
     * Actualiza la información de un beat existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el beat no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
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
            // id_usuario NO se cambia
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

    /**
     * Elimina un beat de la base de datos.
     *
     * @param  int  $id  Identificador único del beat.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si el beat no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
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
