<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use Illuminate\Http\Request;

class StudioColeccionController extends Controller
{
    private function canManage(Coleccion $coleccion): bool
    {
        $usuario = auth()->user();

        return $coleccion->id_usuario === $usuario->id || $usuario->esAdmin();
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
            'tipo_coleccion'   => 'required',
            'precio'           => 'nullable|numeric|min:0',
        ]);

        $coleccion = Coleccion::create([
            'id_usuario'            => auth()->id(),
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => $request->input('precio', 0),
            'es_destacada'          => $request->has('es_destacada'),
            'activo_publicado'      => $request->has('activo_publicado'),
            'fecha_creacion'        => now(),
        ]);

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
            'tipo_coleccion'   => 'required',
            'precio'           => 'nullable|numeric|min:0',
        ]);

        $coleccion->update([
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => $request->input('precio', 0),
            'es_destacada'          => $request->has('es_destacada'),
            'activo_publicado'      => $request->has('activo_publicado'),
        ]);

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
