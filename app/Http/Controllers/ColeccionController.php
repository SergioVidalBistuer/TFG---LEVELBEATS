<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleccion;
use App\Models\Usuario;
use App\Models\Beat;
use App\Models\Guardado;
use App\Support\LicenciaCompra;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ColeccionController extends Controller
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

    // LISTADO
    public function index(Request $request)
    {
        $query = Coleccion::with('usuario', 'beats')->withCount('beats')->publicadas();

        $query
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('titulo_coleccion', 'like', '%' . $request->input('q') . '%');
            })
            ->when($request->filled('tipo'), function ($q) use ($request) {
                $q->where('tipo_coleccion', $request->input('tipo'));
            })
            ->when($request->filled('genero'), function ($q) use ($request) {
                $q->where('estilo_genero', $request->input('genero'));
            })
            ->when($request->filled('precio_min'), function ($q) use ($request) {
                $q->where('precio', '>=', $request->input('precio_min'));
            })
            ->when($request->filled('precio_max'), function ($q) use ($request) {
                $q->where('precio', '<=', $request->input('precio_max'));
            })
            ->when($request->filled('beats_min'), function ($q) use ($request) {
                $q->has('beats', '>=', (int) $request->input('beats_min'));
            })
            ->when($request->filled('beats_max'), function ($q) use ($request) {
                $q->has('beats', '<=', (int) $request->input('beats_max'));
            });

        $colecciones = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $opcionesFiltro = [
            'tipos' => Coleccion::publicadas()->whereNotNull('tipo_coleccion')->where('tipo_coleccion', '!=', '')->distinct()->orderBy('tipo_coleccion')->pluck('tipo_coleccion'),
            'generos' => Coleccion::publicadas()->whereNotNull('estilo_genero')->where('estilo_genero', '!=', '')->distinct()->orderBy('estilo_genero')->pluck('estilo_genero'),
        ];

        // IDs de colecciones que el usuario autenticado ya tiene guardadas
        $guardadosIds = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'coleccion')
                ->pluck('guardable_id')
                ->toArray()
            : [];

        return view('coleccion.index', compact('colecciones', 'opcionesFiltro', 'guardadosIds'));
    }

    // DETALLE
    public function detail($id)
    {
        $coleccion = Coleccion::with('beats', 'usuario')->findOrFail($id);
        if (!$coleccion->activo_publicado && (!auth()->check() || (!auth()->user()->esAdmin() && $coleccion->id_usuario !== auth()->id()))) {
            abort(404);
        }

        $licenciasCompra = LicenciaCompra::opciones();
        $exclusivaVendida = LicenciaCompra::exclusivaVendida('coleccion', $coleccion->id);

        $estaGuardado = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'coleccion')
                ->where('guardable_id', $coleccion->id)
                ->exists()
            : false;

        return view('coleccion.detail', compact('coleccion', 'licenciasCompra', 'exclusivaVendida', 'estaGuardado'));
    }

    // FORMULARIO CREAR
    public function create()
    {
        // Solo los beats del usuario logueado
        $beats = Beat::where('id_usuario', auth()->id())->get();

        return view('coleccion.create', compact('beats'));
    }

    // GUARDAR
    public function save(Request $request)
    {
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

        // Relación N:N con beats
        if ($request->has('beats')) {
            $coleccion->beats()->attach($request->beats);
        }

        return redirect()->route('coleccion.index')
            ->with('status', 'Colección creada correctamente');
    }

    // FORMULARIO EDITAR
    public function edit($id)
    {
        $coleccion = Coleccion::with('beats')->findOrFail($id);

        // Solo el propietario o admin puede editar
        if (!auth()->user()->esAdmin() && $coleccion->id_usuario !== auth()->id()) {
            abort(403);
        }

        // Solo los beats del propietario de la colección
        $beats = Beat::where('id_usuario', $coleccion->id_usuario)->get();

        return view('coleccion.create', compact('coleccion', 'beats'));
    }

    // ACTUALIZAR
    public function update(Request $request)
    {
        $coleccion = Coleccion::findOrFail($request->id);

        // Solo el propietario o admin puede actualizar
        if (!auth()->user()->esAdmin() && $coleccion->id_usuario !== auth()->id()) {
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

        // Sincronizar N:N
        if ($request->has('beats')) {
            $coleccion->beats()->sync($request->beats);
        } else {
            $coleccion->beats()->detach();
        }

        return redirect()->route('coleccion.index')
            ->with('status', 'Colección actualizada correctamente');
    }

    // ELIMINAR
    public function delete($id)
    {
        $coleccion = Coleccion::findOrFail($id);

        // Solo el propietario o admin puede eliminar
        if (!auth()->user()->esAdmin() && $coleccion->id_usuario !== auth()->id()) {
            abort(403);
        }

        $coleccion->beats()->detach(); // Limpiar pivot
        $coleccion->delete();

        return redirect()->route('coleccion.index')
            ->with('status', 'Colección eliminada correctamente');
    }
}
