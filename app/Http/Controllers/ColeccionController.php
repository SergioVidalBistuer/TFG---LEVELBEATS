<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleccion;
use App\Models\Usuario;
use App\Models\Beat;

class ColeccionController extends Controller
{
    // LISTADO
    public function index()
    {
        $colecciones = Coleccion::with('usuario', 'beats')->paginate(12);

        return view('coleccion.index', compact('colecciones'));
    }

    // DETALLE
    public function detail($id)
    {
        $coleccion = Coleccion::with('beats', 'usuario')->findOrFail($id);

        return view('coleccion.detail', compact('coleccion'));
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
            'tipo_coleccion'   => 'required',
        ]);

        $coleccion = Coleccion::create([
            'id_usuario'            => auth()->id(),
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'es_destacada'          => $request->has('es_destacada'),
            'fecha_creacion'        => now(),
        ]);

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

        $coleccion->update([
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'es_destacada'          => $request->has('es_destacada'),
        ]);

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

