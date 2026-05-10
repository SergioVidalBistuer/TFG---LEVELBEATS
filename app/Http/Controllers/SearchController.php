<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Servicio;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $termino = trim((string) $request->input('q', ''));

        $beats = collect();
        $colecciones = collect();
        $servicios = collect();

        if ($termino !== '') {
            $like = '%' . $termino . '%';

            $beats = Beat::with('usuario')
                ->publicados()
                ->where('titulo_beat', 'like', $like)
                ->orderByDesc('id')
                ->limit(8)
                ->get();

            $colecciones = Coleccion::with('usuario', 'beats')
                ->publicadas()
                ->where('titulo_coleccion', 'like', $like)
                ->orderByDesc('id')
                ->limit(8)
                ->get();

            $servicios = Servicio::with('usuario')
                ->where('servicio_activo', 1)
                ->where('titulo_servicio', 'like', $like)
                ->orderByDesc('id')
                ->limit(8)
                ->get();
        }

        return view('search.index', compact('termino', 'beats', 'colecciones', 'servicios'));
    }
}
