<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Guardado;
use App\Models\Proyecto;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Controlador de analíticas personales y profesionales.
 *
 * Calcula métricas en tiempo real para usuarios básicos, productores e
 * ingenieros utilizando compras, productos, servicios, proyectos y guardados.
 */
class AnaliticaController extends Controller
{
    /**
     * Muestra la página única de analíticas adaptada a los roles del usuario.
     */
    public function index()
    {
        $usuario = auth()->user();
        $usuarioId = auth()->id();
        $esProductor = $usuario->tieneRol('productor');
        $esIngeniero = $usuario->tieneRol('ingeniero');

        $generales = $this->metricasGenerales($usuarioId);
        $productor = $esProductor ? $this->metricasProductor($usuarioId) : null;
        $ingeniero = $esIngeniero ? $this->metricasIngeniero($usuarioId) : null;

        $subtitulo = match (true) {
            $esProductor && $esIngeniero => 'Resumen profesional de tu actividad en Studio.',
            $esProductor => 'Rendimiento de tu catálogo musical.',
            $esIngeniero => 'Rendimiento de tus servicios y proyectos.',
            default => 'Resumen de tu actividad en LevelBeats.',
        };

        return view('analiticas.index', compact(
            'usuario',
            'esProductor',
            'esIngeniero',
            'generales',
            'productor',
            'ingeniero',
            'subtitulo'
        ));
    }

    /**
     * Calcula indicadores generales de actividad del comprador.
     */
    private function metricasGenerales(int $usuarioId): array
    {
        $compras = Compra::where('id_usuario_comprador', $usuarioId);
        $compraIds = (clone $compras)->pluck('id');

        return [
            'compras_realizadas' => (clone $compras)->count(),
            'total_gastado' => (float) (clone $compras)->where('estado_compra', 'pagada')->sum('importe_total'),
            'beats_adquiridos' => Schema::hasTable('compra_detalle')
                ? CompraDetalle::whereIn('id_compra', $compraIds)->where('tipo_producto', 'beat')->count()
                : 0,
            'colecciones_adquiridas' => Schema::hasTable('compra_detalle')
                ? CompraDetalle::whereIn('id_compra', $compraIds)->where('tipo_producto', 'coleccion')->count()
                : 0,
            'servicios_contratados' => Schema::hasTable('proyecto')
                ? Proyecto::where('id_usuario', $usuarioId)->count()
                : 0,
            'conversaciones' => Schema::hasTable('conversacion')
                ? DB::table('conversacion')->where('usuario_uno_id', $usuarioId)->orWhere('usuario_dos_id', $usuarioId)->count()
                : 0,
            'guardados' => Schema::hasTable('guardados')
                ? Guardado::where('id_usuario', $usuarioId)->count()
                : 0,
            'ultimas_compras' => Compra::with('detalles')
                ->where('id_usuario_comprador', $usuarioId)
                ->orderByDesc('fecha_compra')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Calcula métricas de catálogo, ventas e ingresos para productores.
     */
    private function metricasProductor(int $usuarioId): array
    {
        $ventasProductor = $this->ventasProductorNormalizadas($usuarioId);
        $ventasBeats = $ventasProductor->where('tipo', 'beat')->count();
        $ventasColecciones = $ventasProductor->where('tipo', 'coleccion')->count();
        $ingresos = (float) $ventasProductor->sum('importe');
        $ventas = $ventasProductor->count();
        $productosMasVendidos = $ventasProductor
            ->groupBy(fn($venta) => $venta->tipo . ':' . $venta->id_producto)
            ->map(function ($grupo) {
                $primeraVenta = $grupo->first();

                return (object) [
                    'tipo' => $primeraVenta->tipo,
                    'id_producto' => $primeraVenta->id_producto,
                    'nombre' => $primeraVenta->nombre,
                    'total' => $grupo->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        return [
            'beats_publicados' => Beat::where('id_usuario', $usuarioId)->where('activo_publicado', true)->count(),
            'beats_ocultos' => Beat::where('id_usuario', $usuarioId)->where('activo_publicado', false)->count(),
            'colecciones_publicadas' => Coleccion::where('id_usuario', $usuarioId)->where('activo_publicado', true)->count(),
            'colecciones_ocultas' => Coleccion::where('id_usuario', $usuarioId)->where('activo_publicado', false)->count(),
            'ventas_beats' => $ventasBeats,
            'ventas_colecciones' => $ventasColecciones,
            'ingresos' => $ingresos,
            'ticket_medio' => $ventas > 0 ? $ingresos / $ventas : 0,
            'ultimas_ventas' => $ventasProductor->sortByDesc('fecha')->take(5)->values(),
            'productos_mas_vendidos' => $productosMasVendidos,
            'guardados_recibidos' => Schema::hasTable('guardados')
                ? $this->guardadosRecibidosProductor($usuarioId)
                : 0,
            'conversaciones' => $this->conversacionesDelUsuario($usuarioId),
        ];
    }

    /**
     * Calcula métricas de servicios, proyectos e ingresos para ingenieros.
     */
    private function metricasIngeniero(int $usuarioId): array
    {
        $servicioIds = Servicio::where('id_usuario', $usuarioId)->pluck('id');
        $ventasServicio = $this->ventasServiciosIngeniero($usuarioId);
        $ingresos = (float) $ventasServicio->sum('importe');
        $ventas = $ventasServicio->count();

        return [
            'servicios_publicados' => Servicio::where('id_usuario', $usuarioId)->count(),
            'servicios_activos' => Servicio::where('id_usuario', $usuarioId)->where('servicio_activo', true)->count(),
            'servicios_inactivos' => Servicio::where('id_usuario', $usuarioId)->where('servicio_activo', false)->count(),
            'proyectos_recibidos' => Proyecto::whereIn('id_servicio', $servicioIds)->count(),
            'proyectos_abiertos' => Proyecto::whereIn('id_servicio', $servicioIds)
                ->whereIn('estado_proyecto', ['pendiente_archivos', 'archivos_recibidos', 'pendiente_aceptacion_ingeniero', 'pendiente_pago_cliente', 'en_proceso', 'en_revision'])
                ->count(),
            'proyectos_cerrados' => Proyecto::whereIn('id_servicio', $servicioIds)
                ->whereIn('estado_proyecto', ['entregado', 'cerrado'])
                ->count(),
            'ingresos' => $ingresos,
            'ticket_medio' => $ventas > 0 ? $ingresos / $ventas : 0,
            'plazo_medio' => (float) Servicio::where('id_usuario', $usuarioId)->avg('plazo_entrega_dias'),
            'ultimos_proyectos' => Proyecto::with(['cliente', 'servicio'])
                ->whereIn('id_servicio', $servicioIds)
                ->orderByDesc('fecha_creacion')
                ->take(5)
                ->get(),
            'servicios_mas_contratados' => $ventasServicio
                ->groupBy('id_producto')
                ->map(function ($grupo) {
                    $primeraVenta = $grupo->first();

                    return (object) [
                        'titulo_servicio' => $primeraVenta->nombre,
                        'total' => $grupo->count(),
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values(),
            'conversaciones' => $this->conversacionesDelUsuario($usuarioId),
        ];
    }

    private function ventasProductorNormalizadas(int $usuarioId)
    {
        return $this->ventasBeatsProductor($usuarioId)
            ->concat($this->ventasColeccionesProductor($usuarioId))
            ->values();
    }

    private function ventasBeatsProductor(int $usuarioId)
    {
        if (!Schema::hasTable('beat_compra')) {
            return collect();
        }

        $query = DB::table('beat_compra')
            ->join('beat', 'beat.id', '=', 'beat_compra.id_beat')
            ->join('compra', 'compra.id', '=', 'beat_compra.id_compra')
            ->leftJoin('usuario as comprador', 'comprador.id', '=', 'compra.id_usuario_comprador')
            ->where('beat.id_usuario', $usuarioId)
            ->where('compra.estado_compra', 'pagada');

        if (Schema::hasTable('compra_detalle')) {
            $query->leftJoin('compra_detalle', function ($join) {
                $join->on('compra_detalle.id_compra', '=', 'compra.id')
                    ->on('compra_detalle.id_producto', '=', 'beat.id')
                    ->where('compra_detalle.tipo_producto', '=', 'beat');
            });

            return $query
                ->selectRaw("
                    'beat' as tipo,
                    beat.id as id_producto,
                    compra.id as id_compra,
                    COALESCE(compra_detalle.nombre_producto_snapshot, beat.titulo_beat) as nombre,
                    COALESCE(compra_detalle.precio_final, beat.precio_base_licencia, 0) as importe,
                    COALESCE(compra_detalle.fecha, compra.fecha_compra) as fecha,
                    comprador.nombre_usuario as comprador
                ")
                ->get();
        }

        return $query
            ->selectRaw("
                'beat' as tipo,
                beat.id as id_producto,
                compra.id as id_compra,
                beat.titulo_beat as nombre,
                COALESCE(beat.precio_base_licencia, 0) as importe,
                compra.fecha_compra as fecha,
                comprador.nombre_usuario as comprador
            ")
            ->get();
    }

    private function ventasColeccionesProductor(int $usuarioId)
    {
        if (!Schema::hasTable('coleccion_compra')) {
            return collect();
        }

        $query = DB::table('coleccion_compra')
            ->join('coleccion', 'coleccion.id', '=', 'coleccion_compra.id_coleccion')
            ->join('compra', 'compra.id', '=', 'coleccion_compra.id_compra')
            ->leftJoin('usuario as comprador', 'comprador.id', '=', 'compra.id_usuario_comprador')
            ->where('coleccion.id_usuario', $usuarioId)
            ->where('compra.estado_compra', 'pagada');

        if (Schema::hasTable('compra_detalle')) {
            $query->leftJoin('compra_detalle', function ($join) {
                $join->on('compra_detalle.id_compra', '=', 'compra.id')
                    ->on('compra_detalle.id_producto', '=', 'coleccion.id')
                    ->where('compra_detalle.tipo_producto', '=', 'coleccion');
            });

            return $query
                ->selectRaw("
                    'coleccion' as tipo,
                    coleccion.id as id_producto,
                    compra.id as id_compra,
                    COALESCE(compra_detalle.nombre_producto_snapshot, coleccion.titulo_coleccion) as nombre,
                    COALESCE(compra_detalle.precio_final, coleccion.precio, 0) as importe,
                    COALESCE(compra_detalle.fecha, compra.fecha_compra) as fecha,
                    comprador.nombre_usuario as comprador
                ")
                ->get();
        }

        return $query
            ->selectRaw("
                'coleccion' as tipo,
                coleccion.id as id_producto,
                compra.id as id_compra,
                coleccion.titulo_coleccion as nombre,
                COALESCE(coleccion.precio, 0) as importe,
                compra.fecha_compra as fecha,
                comprador.nombre_usuario as comprador
            ")
            ->get();
    }

    private function ventasServiciosIngeniero(int $usuarioId)
    {
        if (!Schema::hasTable('servicio_compra')) {
            return collect();
        }

        return DB::table('servicio_compra')
            ->join('servicio', 'servicio.id', '=', 'servicio_compra.id_servicio')
            ->join('compra', 'compra.id', '=', 'servicio_compra.id_compra')
            ->leftJoin('usuario as comprador', 'comprador.id', '=', 'compra.id_usuario_comprador')
            ->where('servicio.id_usuario', $usuarioId)
            ->where('compra.estado_compra', 'pagada')
            ->selectRaw("
                'servicio' as tipo,
                servicio.id as id_producto,
                compra.id as id_compra,
                servicio.titulo_servicio as nombre,
                compra.importe_total as importe,
                compra.fecha_compra as fecha,
                comprador.nombre_usuario as comprador
            ")
            ->get();
    }

    private function conversacionesDelUsuario(int $usuarioId): int
    {
        if (!Schema::hasTable('conversacion')) {
            return 0;
        }

        return DB::table('conversacion')
            ->where(function ($q) use ($usuarioId) {
                $q->where('usuario_uno_id', $usuarioId)->orWhere('usuario_dos_id', $usuarioId);
            })
            ->count();
    }

    private function guardadosRecibidosProductor(int $usuarioId): int
    {
        $beatIds = Beat::where('id_usuario', $usuarioId)->pluck('id');
        $coleccionIds = Coleccion::where('id_usuario', $usuarioId)->pluck('id');

        return Guardado::where(function ($q) use ($beatIds, $coleccionIds) {
            $q->where(function ($sub) use ($beatIds) {
                $sub->where('guardable_type', 'beat')->whereIn('guardable_id', $beatIds);
            })->orWhere(function ($sub) use ($coleccionIds) {
                $sub->where('guardable_type', 'coleccion')->whereIn('guardable_id', $coleccionIds);
            });
        })->count();
    }
}
