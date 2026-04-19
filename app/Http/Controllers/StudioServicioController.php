<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Auditoria;

class StudioServicioController extends Controller
{
    /**
     * Asegura que el creador solo gestione sus propios servicios.
     */
    private function canManage(Servicio $servicio): bool
    {
        $usuario = auth()->user();
        return $servicio->id_usuario === $usuario->id || $usuario->esAdmin();
    }

    public function index()
    {
        abort_unless(auth()->user()->tieneRol('ingeniero') || auth()->user()->esAdmin(), 403, 'Acceso exclusivo para Ingenieros');
        
        // El usuario ve SOLO sus propios servicios ofertados:
        $servicios = Servicio::where('id_usuario', auth()->id())->orderBy('id', 'desc')->get();
        return view('studio.servicios.index', compact('servicios'));
    }

    public function create()
    {
        abort_unless(auth()->user()->tieneRol('ingeniero') || auth()->user()->esAdmin(), 403);
        
        $usuario = auth()->user();
        if (!$usuario->esAdmin()) {
            $rolIngeniero = \App\Models\Rol::where('nombre_rol', 'ingeniero')->first();
            $sub = $usuario->suscripciones()->with('planPorRol')
                           ->where('id_rol', $rolIngeniero->id)
                           ->where('estado_suscripcion', 'activa')
                           ->latest('fecha_inicio')->first();
            
            if (!$sub) {
                return redirect()->route('onboarding.planes', ['rol' => 'ingeniero'])
                                 ->with('status', 'Aviso: Necesitas validar un Plan para ofertar Servicios técnicos.');
            }

            $limite = $sub->planPorRol->encargos_max_ingeniero;
            $serviciosActuales = Servicio::where('id_usuario', $usuario->id)->count();

            if ($serviciosActuales >= $limite && $limite < 90) { 
                return redirect()->route('studio.servicios.index')
                                 ->with('status', "Carga máxima ($limite) alcanzada. Actualiza tu plan para ampliar tu Studio.");
            }
        }

        return view('studio.servicios.form');
    }

    public function save(Request $request)
    {
        $usuario = auth()->user();
        if (!$usuario->esAdmin()) {
            $rolIngeniero = \App\Models\Rol::where('nombre_rol', 'ingeniero')->first();
            $sub = $usuario->suscripciones()->with('planPorRol')
                           ->where('id_rol', $rolIngeniero->id)
                           ->where('estado_suscripcion', 'activa')
                           ->latest('fecha_inicio')->first();
            
            $limite = $sub ? $sub->planPorRol->encargos_max_ingeniero : 0;
            $serviciosActuales = Servicio::where('id_usuario', $usuario->id)->count();

            if (!$sub || ($serviciosActuales >= $limite && $limite < 90)) {
                return redirect()->route('studio.servicios.index')->with('status', "Plan insuficiente.");
            }
        }

        $request->validate([
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
        ]);

        $servicio = Servicio::create([
            'id_usuario'           => auth()->id(),
            'titulo_servicio'      => $request->titulo_servicio,
            'tipo_servicio'        => $request->tipo_servicio,
            'descripcion_servicio' => $request->descripcion_servicio,
            'precio_servicio'      => $request->precio_servicio,
            'plazo_entrega_dias'   => $request->plazo_entrega_dias,
            'numero_revisiones'    => $request->numero_revisiones,
            'url_portafolio'       => $request->url_portafolio,
            'servicio_activo'      => $request->has('servicio_activo'),
        ]);

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'crear',
            'entidad' => 'servicio',
            'id_entidad' => $servicio->id,
            'fecha' => now(),
        ]);

        return redirect()->route('studio.servicios.index')->with('status', 'Servicio subido con éxito a tu catálogo técnico.');
    }

    public function edit($id)
    {
        $servicio = Servicio::findOrFail($id);
        if (!$this->canManage($servicio)) {
            abort(403, 'Acceso denegado a este servicio.');
        }

        return view('studio.servicios.form', compact('servicio'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                   => 'required|integer',
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
        ]);

        $servicio = Servicio::findOrFail($request->id);
        if (!$this->canManage($servicio)) {
            abort(403);
        }

        $servicio->update([
            'titulo_servicio'      => $request->titulo_servicio,
            'tipo_servicio'        => $request->tipo_servicio,
            'descripcion_servicio' => $request->descripcion_servicio,
            'precio_servicio'      => $request->precio_servicio,
            'plazo_entrega_dias'   => $request->plazo_entrega_dias,
            'numero_revisiones'    => $request->numero_revisiones,
            'url_portafolio'       => $request->url_portafolio,
            'servicio_activo'      => $request->has('servicio_activo'),
        ]);

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'actualizar',
            'entidad' => 'servicio',
            'id_entidad' => $servicio->id,
            'fecha' => now(),
        ]);

        return redirect()->route('studio.servicios.index')->with('status', 'Servicio actualizado con éxito.');
    }

    public function delete($id)
    {
        $servicio = Servicio::findOrFail($id);
        if (!$this->canManage($servicio)) {
            abort(403);
        }

        $id_entidad = $servicio->id;
        $servicio->delete();

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion' => 'eliminar',
            'entidad' => 'servicio',
            'id_entidad' => $id_entidad,
            'fecha' => now(),
        ]);
        return redirect()->route('studio.servicios.index')->with('status', 'Servicio retirado de tu catálogo.');
    }
}
