<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Beat;
use App\Models\Compra;
use App\Models\Proyecto;
use App\Models\Servicio;
use App\Models\Coleccion;

/**
 * Controlador del dashboard administrativo.
 */
class AdminDashboardController extends Controller
{
    /**
     * Calcula contadores globales para el panel root/admin.
     */
    public function index()
    {
        // Estadísticas básicas generales
        $totalUsuarios = Usuario::count();
        $totalBeats = Beat::count();
        $totalCompras = Compra::count();
        $totalProyectos = Proyecto::count();
        $totalServicios = Servicio::count();
        $totalColecciones = Coleccion::count();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalBeats',
            'totalCompras',
            'totalProyectos',
            'totalServicios',
            'totalColecciones'
        ));
    }
}
