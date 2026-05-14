<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\MensajeDirecto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MensajeDirectoController extends Controller
{
    private function tablasDisponibles(): bool
    {
        return Schema::hasTable('conversacion') && Schema::hasTable('mensaje_directo');
    }

    private function abortarSiFaltanTablas()
    {
        if (!$this->tablasDisponibles()) {
            return redirect()->route('home.index')
                ->with('status', 'El módulo de mensajes directos necesita crear sus tablas en la base de datos.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->abortarSiFaltanTablas()) {
            return $redirect;
        }

        $usuarioId = auth()->id();

        $conversaciones = Conversacion::query()
            ->where(function ($query) use ($usuarioId) {
                $query->where('usuario_uno_id', $usuarioId)
                    ->orWhere('usuario_dos_id', $usuarioId);
            })
            ->with(['usuarioUno', 'usuarioDos', 'ultimoMensaje.emisor'])
            ->orderByDesc('ultimo_mensaje_at')
            ->orderByDesc('fecha_creacion')
            ->get();

        return view('mensajes.index', compact('conversaciones'));
    }

    public function start(Usuario $usuario)
    {
        if ($redirect = $this->abortarSiFaltanTablas()) {
            return $redirect;
        }

        abort_if(auth()->id() === $usuario->id, 403);

        $ids = [auth()->id(), $usuario->id];
        sort($ids);

        $conversacion = Conversacion::firstOrCreate(
            [
                'usuario_uno_id' => $ids[0],
                'usuario_dos_id' => $ids[1],
            ],
            [
                'fecha_creacion' => now(),
            ]
        );

        return redirect()->route('mensajes.show', $conversacion);
    }

    public function show($conversacion)
    {
        if ($redirect = $this->abortarSiFaltanTablas()) {
            return $redirect;
        }

        $conversacion = Conversacion::findOrFail($conversacion);

        abort_unless($conversacion->participa(auth()->id()), 403);

        $conversacion->load(['usuarioUno', 'usuarioDos', 'mensajes.emisor']);

        MensajeDirecto::where('conversacion_id', $conversacion->id)
            ->where('emisor_id', '<>', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true]);

        $otroUsuario = $conversacion->otroUsuario(auth()->id());

        return view('mensajes.show', compact('conversacion', 'otroUsuario'));
    }

    public function send(Request $request, $conversacion)
    {
        if ($redirect = $this->abortarSiFaltanTablas()) {
            return $redirect;
        }

        $conversacion = Conversacion::findOrFail($conversacion);

        abort_unless($conversacion->participa(auth()->id()), 403);

        $request->validate([
            'cuerpo' => 'required|string|max:2000',
        ], [
            'cuerpo.required' => 'Escribe un mensaje antes de enviarlo.',
            'cuerpo.max' => 'El mensaje no puede superar los :max caracteres.',
        ]);

        MensajeDirecto::create([
            'conversacion_id' => $conversacion->id,
            'emisor_id' => auth()->id(),
            'cuerpo' => $request->input('cuerpo'),
            'leido' => false,
            'fecha_envio' => now(),
        ]);

        $conversacion->update(['ultimo_mensaje_at' => now()]);

        return redirect()->route('mensajes.show', $conversacion);
    }
}
