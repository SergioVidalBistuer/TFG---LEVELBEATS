<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;

class AdminAuditoriaController extends Controller
{
    public function index()
    {
        // La protección de admin ya está en el middleware de la ruta
        $registros = Auditoria::with('actor')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.auditoria.index', compact('registros'));
    }
}
