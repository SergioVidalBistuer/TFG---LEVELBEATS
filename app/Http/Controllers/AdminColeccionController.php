<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;

/**
 * Controlador administrativo de colecciones.
 */
class AdminColeccionController extends Controller
{
    /**
     * Lista todas las colecciones registradas con propietario y número de beats.
     */
    public function index()
    {
        $colecciones = Coleccion::with('usuario', 'beats')
            ->withCount('beats')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.colecciones.index', compact('colecciones'));
    }
}
