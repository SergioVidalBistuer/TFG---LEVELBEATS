<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;

class AdminColeccionController extends Controller
{
    public function index()
    {
        $colecciones = Coleccion::with('usuario', 'beats')
            ->withCount('beats')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.colecciones.index', compact('colecciones'));
    }
}
