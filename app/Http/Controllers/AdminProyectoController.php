<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

class AdminProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::with(['cliente', 'servicio.usuario'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.proyectos.index', compact('proyectos'));
    }
}
