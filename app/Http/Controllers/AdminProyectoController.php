<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;

/**
 * Controlador administrativo de proyectos.
 */
class AdminProyectoController extends Controller
{
    /**
     * Lista todos los proyectos junto a cliente, servicio e ingeniero.
     */
    public function index()
    {
        $proyectos = Proyecto::with(['cliente', 'servicio.usuario'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.proyectos.index', compact('proyectos'));
    }
}
