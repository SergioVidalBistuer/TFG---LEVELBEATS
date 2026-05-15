<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Guardado;
use App\Models\Mensaje;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;

/**
 * Controlador del marketplace público de servicios profesionales.
 *
 * Permite descubrir servicios activos, consultar su detalle y crear el primer
 * contacto/proyecto asociado entre cliente e ingeniero.
 */
class ServicioController extends Controller
{
    /**
     * Catálogo público de servicios ofertados por Ingenieros.
     * Solo muestra servicios activos (servicio_activo = 1).
     */
    public function index(Request $request)
    {
        $query = Servicio::with('usuario')
            ->where('servicio_activo', 1);

        $query
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('titulo_servicio', 'like', '%' . $request->input('q') . '%');
            })
            ->when($request->filled('tipo'), function ($q) use ($request) {
                $q->where('tipo_servicio', $request->input('tipo'));
            })
            ->when($request->filled('precio_min'), function ($q) use ($request) {
                $q->where('precio_servicio', '>=', $request->input('precio_min'));
            })
            ->when($request->filled('precio_max'), function ($q) use ($request) {
                $q->where('precio_servicio', '<=', $request->input('precio_max'));
            })
            ->when($request->filled('plazo_max'), function ($q) use ($request) {
                $q->where('plazo_entrega_dias', '<=', $request->input('plazo_max'));
            })
            ->when($request->filled('revisiones_min'), function ($q) use ($request) {
                $q->where('numero_revisiones', '>=', $request->input('revisiones_min'));
            });

        $servicios = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        $opcionesFiltro = [
            'tipos' => Servicio::where('servicio_activo', 1)
                ->whereNotNull('tipo_servicio')
                ->where('tipo_servicio', '!=', '')
                ->distinct()
                ->orderBy('tipo_servicio')
                ->pluck('tipo_servicio'),
        ];

        // IDs de servicios que el usuario autenticado ya tiene guardados
        $guardadosIds = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'servicio')
                ->pluck('guardable_id')
                ->toArray()
            : [];

        return view('servicio.index', compact('servicios', 'opcionesFiltro', 'guardadosIds'));
    }

    /**
     * Detalle público de un servicio con toda la info del ingeniero.
     */
    public function detail($id)
    {
        $servicio = Servicio::with('usuario')->findOrFail($id);
        if (!$servicio->servicio_activo && (!auth()->check() || (!auth()->user()->esAdmin() && $servicio->id_usuario !== auth()->id()))) {
            abort(404);
        }

        $estaGuardado = auth()->check()
            ? Guardado::where('id_usuario', auth()->id())
                ->where('guardable_type', 'servicio')
                ->where('guardable_id', $servicio->id)
                ->exists()
            : false;

        return view('servicio.detail', compact('servicio', 'estaGuardado'));
    }

    /**
     * Crea un encargo real a partir de una solicitud de servicio y abre
     * el primer mensaje del hilo asociado al proyecto.
     */
    public function contacto(Request $request, $id)
    {
        $servicio = Servicio::with('usuario')->where('servicio_activo', 1)->findOrFail($id);

        // Evitar que el propio ingeniero solicite su propio servicio.
        if (auth()->id() === $servicio->id_usuario) {
            return back()->with('status', 'No puedes contactarte a ti mismo.');
        }

        $request->validate([
            'mensaje' => 'required|string|max:1000',
        ]);

        $proyecto = DB::transaction(function () use ($servicio, $request) {
            $proyecto = Proyecto::create([
                'id_usuario'            => auth()->id(),
                'id_servicio'           => $servicio->id,
                'titulo_proyecto'       => 'Encargo: ' . $servicio->titulo_servicio,
                'estado_proyecto'       => 'pendiente_aceptacion_ingeniero',
                'notas_proyecto'        => null,
                'ruta_carpeta_archivos' => null,
                'fecha_creacion'        => now(),
            ]);

            Mensaje::create([
                'id_usuario_emisor'   => auth()->id(),
                'id_usuario_receptor' => $servicio->id_usuario,
                'id_proyecto'         => $proyecto->id,
                'contenido_mensaje'   => $request->mensaje,
                'fecha_envio'         => now(),
                'mensaje_leido'       => 0,
            ]);

            return $proyecto;
        });

        return redirect()
            ->route('usuario.encargos.detail', $proyecto->id)
            ->with('status', 'Servicio solicitado correctamente. Se ha creado tu encargo.');
    }
}
