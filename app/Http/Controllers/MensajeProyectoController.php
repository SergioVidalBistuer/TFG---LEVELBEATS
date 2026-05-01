<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Mensaje;

class MensajeProyectoController extends Controller
{
    public function enviar(Request $request, $id_proyecto)
    {
        $request->validate([
            'contenido_mensaje' => 'required|string|max:1000'
        ]);

        $proyecto = Proyecto::with('servicio')->findOrFail($id_proyecto);
        $usuario_actual = auth()->id();

        // Determinar permisos y receptor
        $esCliente = ($proyecto->id_usuario === $usuario_actual);
        $esIngeniero = ($proyecto->servicio && $proyecto->servicio->id_usuario === $usuario_actual);
        
        if (!$esCliente && !$esIngeniero && !auth()->user()->esAdmin()) {
            abort(403, 'Acceso denegado a este canal de comunicación.');
        }

        // Si soy yo el cliente, se lo envío al ingeniero. Si soy el ingeniero, se lo envío al cliente.
        $id_receptor = $esCliente ? $proyecto->servicio->id_usuario : $proyecto->id_usuario;

        Mensaje::create([
            'id_usuario_emisor'   => $usuario_actual,
            'id_usuario_receptor' => $id_receptor,
            'id_proyecto'         => $id_proyecto,
            'contenido_mensaje'   => $request->contenido_mensaje,
            'fecha_envio'         => now(),
        ]);

        return back()->with('status', 'Mensaje enviado correctamente.');
    }
}
