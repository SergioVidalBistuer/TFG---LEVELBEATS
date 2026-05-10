<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;

class HomeController extends Controller
{
    public function index()
    {
        $beatsPopulares = Beat::publicados()
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        $colecciones = Coleccion::withCount('beats')
            ->publicadas()
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        return view('home.index', compact('beatsPopulares', 'colecciones'));
    }
}
