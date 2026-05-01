<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Auditoria;

class AdminServicioController extends Controller
{
    /**
     * Listado global de todos los servicios de la plataforma.
     */
    public function index()
    {
        $servicios = Servicio::with('usuario')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.servicios.index', compact('servicios'));
    }

    /**
     * Formulario para crear un nuevo servicio (asignado a cualquier ingeniero).
     */
    public function create()
    {
        $ingenieros = $this->getIngenieros();
        return view('admin.servicios.form', compact('ingenieros'));
    }

    /**
     * Persiste el nuevo servicio sin restricciones de plan.
     */
    public function save(Request $request)
    {
        $request->validate([
            'id_usuario'           => 'required|integer|exists:usuario,id',
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric|min:0',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
        ]);

        $servicio = Servicio::create([
            'id_usuario'           => $request->id_usuario,
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
            'tipo_accion'      => 'crear',
            'entidad'          => 'servicio',
            'id_entidad'       => $servicio->id,
            'fecha'            => now(),
        ]);

        return redirect()->route('admin.servicios.index')
            ->with('status', 'Servicio creado correctamente.');
    }

    /**
     * Formulario de edición de cualquier servicio.
     */
    public function edit($id)
    {
        $servicio    = Servicio::findOrFail($id);
        $ingenieros  = $this->getIngenieros();

        return view('admin.servicios.form', compact('servicio', 'ingenieros'));
    }

    /**
     * Actualiza el servicio sin restricciones.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'                   => 'required|integer',
            'id_usuario'           => 'required|integer|exists:usuario,id',
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric|min:0',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
        ]);

        $servicio = Servicio::findOrFail($request->id);

        $servicio->update([
            'id_usuario'           => $request->id_usuario,
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
            'tipo_accion'      => 'actualizar',
            'entidad'          => 'servicio',
            'id_entidad'       => $servicio->id,
            'fecha'            => now(),
        ]);

        return redirect()->route('admin.servicios.index')
            ->with('status', 'Servicio actualizado correctamente.');
    }

    /**
     * Elimina el servicio con registro de auditoría.
     */
    public function delete($id)
    {
        $servicio   = Servicio::findOrFail($id);
        $id_entidad = $servicio->id;
        $servicio->delete();

        Auditoria::create([
            'id_usuario_actor' => auth()->id(),
            'tipo_accion'      => 'eliminar',
            'entidad'          => 'servicio',
            'id_entidad'       => $id_entidad,
            'fecha'            => now(),
        ]);

        return redirect()->route('admin.servicios.index')
            ->with('status', 'Servicio eliminado correctamente.');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Devuelve los usuarios que tienen el rol "ingeniero" activo.
     */
    private function getIngenieros()
    {
        $rolIngeniero = Rol::where('nombre_rol', 'ingeniero')->first();

        if (!$rolIngeniero) {
            return collect();
        }

        return Usuario::whereHas('roles', function ($q) use ($rolIngeniero) {
            $q->where('usuario_rol.id_rol', $rolIngeniero->id)
              ->where('usuario_rol.rol_activo', 1);
        })->orderBy('nombre_usuario')->get();
    }
}
