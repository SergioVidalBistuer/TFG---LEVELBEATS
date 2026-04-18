<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coleccion;
use App\Models\Usuario;
use App\Models\Beat;

/**
 * Class ColeccionController
 *
 * Controlador para la gestión de las colecciones de beats, incluyendo
 * su creación, visualización, edición y borrado.
 */
class ColeccionController extends Controller
{
    // LISTADO
    /**
     * Muestra un listado paginado de todas las colecciones.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $colecciones = Coleccion::with('usuario', 'beats')->paginate(12);

        return view('coleccion.index', compact('colecciones'));
    }

    // DETALLE
    /**
     * Muestra el detalle de una colección específica.
     *
     * @param  int  $id  Identificador único de la colección.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la colección no existe.
     */
    public function detail($id)
    {
        $coleccion = Coleccion::with('beats', 'usuario')->findOrFail($id);

        return view('coleccion.detail', compact('coleccion'));
    }

    // FORMULARIO CREAR
    /**
     * Muestra el formulario para crear una nueva colección.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Solo los beats del usuario logueado
        $beats = Beat::where('id_usuario', session('usuario_id'))->get();

        return view('coleccion.create', compact('beats'));
    }

    // GUARDAR
    /**
     * Guarda una nueva colección en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos de la colección.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function save(Request $request)
    {
        $request->validate([
            'titulo_coleccion' => 'required|max:140',
            'tipo_coleccion'   => 'required',
        ]);

        // Convertir precio: aceptar tanto "29.99" como "29,99"
        $precio = str_replace(',', '.', $request->input('precio', 0));

        $coleccion = Coleccion::create([
            'id_usuario'            => session('usuario_id'),
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => (float) $precio,
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
    /**
     * Muestra el formulario para editar una colección existente.
     *
     * @param  int  $id  Identificador único de la colección.
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la colección no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function edit($id)
    {
        $coleccion = Coleccion::with('beats')->findOrFail($id);

        // Solo el propietario o admin puede editar
        if (session('rol') !== 'admin' && $coleccion->id_usuario !== session('usuario_id')) {
            abort(403);
        }

        // Solo los beats del propietario de la colección
        $beats = Beat::where('id_usuario', $coleccion->id_usuario)->get();

        return view('coleccion.create', compact('coleccion', 'beats'));
    }

    // ACTUALIZAR
    /**
     * Actualiza la información de una colección en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con los datos a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la colección no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function update(Request $request)
    {
        $coleccion = Coleccion::findOrFail($request->id);

        // Solo el propietario o admin puede actualizar
        if (session('rol') !== 'admin' && $coleccion->id_usuario !== session('usuario_id')) {
            abort(403);
        }

        // Convertir precio
        $precio = str_replace(',', '.', $request->input('precio', 0));

        $coleccion->update([
            'titulo_coleccion'      => $request->titulo_coleccion,
            'tipo_coleccion'        => $request->tipo_coleccion,
            'descripcion_coleccion' => $request->descripcion_coleccion,
            'estilo_genero'         => $request->estilo_genero,
            'precio'                => (float) $precio,
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
    /**
     * Elimina una colección de la base de datos.
     *
     * @param  int  $id  Identificador único de la colección.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la colección no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos (403).
     */
    public function delete($id)
    {
        $coleccion = Coleccion::findOrFail($id);

        // Solo el propietario o admin puede eliminar
        if (session('rol') !== 'admin' && $coleccion->id_usuario !== session('usuario_id')) {
            abort(403);
        }

        $coleccion->beats()->detach(); // Limpiar pivot
        $coleccion->delete();

        return redirect()->route('coleccion.index')
            ->with('status', 'Colección eliminada correctamente');
    }
}
