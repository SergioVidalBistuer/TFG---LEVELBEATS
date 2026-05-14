<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudioColeccionController extends Controller
{
    private const TIPOS_COLECCION = ['publica', 'privada'];

    private const GENEROS_COLECCION = [
        'Trap',
        'Drill',
        'Lo-Fi',
        'Boom Bap',
        'R&B',
        'Pop',
        'Reggaeton',
        'Afrobeat',
        'Electronic',
        'Otro',
    ];

    private function canManage(Coleccion $coleccion): bool
    {
        $usuario = auth()->user();

        return $coleccion->id_usuario === $usuario->id || $usuario->esAdmin();
    }

    private function coleccionTienePortada(): bool
    {
        return filled(Coleccion::portadaColumn());
    }

    private function guardarPortada(Request $request): ?string
    {
        if (!$request->hasFile('portada_coleccion')) {
            return null;
        }

        $archivo = $request->file('portada_coleccion');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('colecciones/covers/' . auth()->id(), $nombre, 'public');

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

        $colecciones = Coleccion::withCount('beats')
            ->with('beats')
            ->where('id_usuario', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return view('studio.colecciones.index', compact('colecciones'));
    }

    public function create()
    {
        abort_unless(auth()->user()->tieneRol('productor') || auth()->user()->esAdmin(), 403);

        $beats = Beat::where('id_usuario', auth()->id())->orderBy('titulo_beat')->get();
        $studioMode = true;

        return view('coleccion.create', compact('beats', 'studioMode'));
    }

    public function save(Request $request)
    {
        abort_unless(auth()->user()->tieneRol('productor') || auth()->user()->esAdmin(), 403);

        $request->validate([
            'titulo_coleccion' => 'required|max:140',
            'tipo_coleccion'   => 'required|in:' . implode(',', self::TIPOS_COLECCION),
            'estilo_genero'    => 'nullable|in:' . implode(',', self::GENEROS_COLECCION),
            'precio'           => 'nullable|numeric|min:0',
            'portada_coleccion' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'tipo_coleccion.in' => 'Selecciona un tipo de colección válido.',
            'estilo_genero.in' => 'Selecciona un género o estilo válido.',
            'portada_coleccion.image' => 'La portada debe ser una imagen válida.',
            'portada_coleccion.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_coleccion.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $datos = [
            'id_usuario'            => auth()->id(),
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => $request->input('precio', 0),
            'es_destacada'          => $request->has('es_destacada'),
            'activo_publicado'      => $request->has('activo_publicado'),
            'fecha_creacion'        => now(),
        ];

        if ($this->coleccionTienePortada() && $request->hasFile('portada_coleccion')) {
            $datos[Coleccion::portadaColumn()] = $this->guardarPortada($request);
        }

        $coleccion = Coleccion::create($datos);

        if ($request->has('beats')) {
            $coleccion->beats()->attach($request->beats);
        }

        return redirect()->route('studio.colecciones.index')
            ->with('status', 'Colección creada correctamente en Studio.');
    }

    public function edit($id)
    {
        $coleccion = Coleccion::with('beats')->findOrFail($id);
        if (!$this->canManage($coleccion)) {
            abort(403);
        }

        $beats = Beat::where('id_usuario', $coleccion->id_usuario)->orderBy('titulo_beat')->get();
        $studioMode = true;

        return view('coleccion.create', compact('coleccion', 'beats', 'studioMode'));
    }

    public function update(Request $request)
    {
        $coleccion = Coleccion::findOrFail($request->id);
        if (!$this->canManage($coleccion)) {
            abort(403);
        }

        $request->validate([
            'titulo_coleccion' => 'required|max:140',
            'tipo_coleccion'   => 'required|in:' . implode(',', self::TIPOS_COLECCION),
            'estilo_genero'    => 'nullable|in:' . implode(',', self::GENEROS_COLECCION),
            'precio'           => 'nullable|numeric|min:0',
            'portada_coleccion' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'tipo_coleccion.in' => 'Selecciona un tipo de colección válido.',
            'estilo_genero.in' => 'Selecciona un género o estilo válido.',
            'portada_coleccion.image' => 'La portada debe ser una imagen válida.',
            'portada_coleccion.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_coleccion.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $datos = [
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => $request->input('precio', 0),
            'es_destacada'          => $request->has('es_destacada'),
            'activo_publicado'      => $request->has('activo_publicado'),
        ];

        if ($this->coleccionTienePortada() && $request->hasFile('portada_coleccion')) {
            $rutaAnterior = $coleccion->portada_url;
            $datos[Coleccion::portadaColumn()] = $this->guardarPortada($request);
            $this->eliminarArchivoPublico($rutaAnterior);
        }

        $coleccion->update($datos);

        if ($request->has('beats')) {
            $coleccion->beats()->sync($request->beats);
        } else {
            $coleccion->beats()->detach();
        }

        return redirect()->route('studio.colecciones.index')
            ->with('status', 'Colección actualizada correctamente.');
    }

    public function delete($id)
    {
        $coleccion = Coleccion::findOrFail($id);
        if (!$this->canManage($coleccion)) {
            abort(403);
        }

        $coleccion->beats()->detach();
        $coleccion->delete();

        return redirect()->route('studio.colecciones.index')
            ->with('status', 'Colección eliminada correctamente.');
    }
}
