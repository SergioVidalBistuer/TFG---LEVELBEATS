<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Beat;
use App\Models\Compra;
use App\Models\Proyecto;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Estadísticas básicas generales
        $totalUsuarios = Usuario::count();
        $totalBeats = Beat::count();
        $totalCompras = Compra::count();
        $totalProyectos = Proyecto::count();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalBeats',
            'totalCompras',
            'totalProyectos'
        ));
    }
}
