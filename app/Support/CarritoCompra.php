<?php

namespace App\Support;

use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Licencia;
use App\Models\PlanPorRol;
use App\Models\Proyecto;
use Illuminate\Support\Collection;

class CarritoCompra
{
    public static function vacio(): array
    {
        return [
            'beats' => [],
            'colecciones' => [],
            'servicios' => [],
            'planes' => [],
        ];
    }

    public static function normalizar(?array $cart): array
    {
        $cart = array_merge(self::vacio(), $cart ?? []);
        $basica = LicenciaCompra::licenciaBasica();

        $normalizado = self::vacio();

        foreach ($cart['beats'] ?? [] as $key => $value) {
            $id = is_array($value) ? (int) ($value['id'] ?? 0) : (int) $key;
            $licenciaId = is_array($value) ? (int) ($value['licencia_id'] ?? 0) : (int) ($basica?->id ?? 0);

            if ($id > 0) {
                $normalizado['beats'][self::clave('beat', $id, $licenciaId)] = [
                    'id' => $id,
                    'licencia_id' => $licenciaId,
                ];
            }
        }

        foreach ($cart['colecciones'] ?? [] as $key => $value) {
            $id = is_array($value) ? (int) ($value['id'] ?? 0) : (int) $key;
            $licenciaId = is_array($value) ? (int) ($value['licencia_id'] ?? 0) : (int) ($basica?->id ?? 0);

            if ($id > 0) {
                $normalizado['colecciones'][self::clave('coleccion', $id, $licenciaId)] = [
                    'id' => $id,
                    'licencia_id' => $licenciaId,
                ];
            }
        }

        foreach ($cart['servicios'] ?? [] as $key => $value) {
            $id = is_array($value) ? (int) ($value['id'] ?? 0) : (int) $key;
            $proyectoId = is_array($value) ? (int) ($value['proyecto_id'] ?? 0) : 0;

            if ($id > 0 && $proyectoId > 0) {
                $normalizado['servicios'][self::claveServicio($id, $proyectoId)] = [
                    'id' => $id,
                    'proyecto_id' => $proyectoId,
                ];
            }
        }

        foreach ($cart['planes'] ?? [] as $key => $value) {
            $id = is_array($value) ? (int) ($value['id'] ?? 0) : (int) $key;

            if ($id > 0) {
                $normalizado['planes'][self::clavePlan($id)] = [
                    'id' => $id,
                ];
            }
        }

        return $normalizado;
    }

    public static function claveServicio(int $id, int $proyectoId): string
    {
        return 'servicio-' . $id . '-proyecto-' . $proyectoId;
    }

    public static function clavePlan(int $id): string
    {
        return 'plan-' . $id;
    }

    public static function clave(string $tipo, int $id, int $licenciaId): string
    {
        return $tipo . '-' . $id . '-lic-' . $licenciaId;
    }

    public static function agregarBeat(array $cart, int $id, int $licenciaId): array
    {
        $cart = self::normalizar($cart);
        $cart['beats'][self::clave('beat', $id, $licenciaId)] = [
            'id' => $id,
            'licencia_id' => $licenciaId,
        ];

        return $cart;
    }

    public static function agregarColeccion(array $cart, int $id, int $licenciaId): array
    {
        $cart = self::normalizar($cart);
        $cart['colecciones'][self::clave('coleccion', $id, $licenciaId)] = [
            'id' => $id,
            'licencia_id' => $licenciaId,
        ];

        return $cart;
    }

    public static function agregarServicio(array $cart, int $id, int $proyectoId): array
    {
        $cart = self::normalizar($cart);
        $cart['servicios'][self::claveServicio($id, $proyectoId)] = [
            'id' => $id,
            'proyecto_id' => $proyectoId,
        ];

        return $cart;
    }

    public static function agregarPlan(array $cart, int $id): array
    {
        $cart = self::vacio();
        $cart['planes'][self::clavePlan($id)] = [
            'id' => $id,
        ];

        return $cart;
    }

    public static function quitar(array $cart, string $tipo, string $clave): array
    {
        $cart = self::normalizar($cart);

        if ($tipo === 'beat') {
            unset($cart['beats'][$clave]);
        }

        if ($tipo === 'coleccion') {
            unset($cart['colecciones'][$clave]);
        }

        if ($tipo === 'servicio') {
            unset($cart['servicios'][$clave]);
        }

        if ($tipo === 'plan') {
            unset($cart['planes'][$clave]);
        }

        return $cart;
    }

    public static function items(array $cart, bool $soloPublicados = true): array
    {
        $cart = self::normalizar($cart);

        $beats = collect($cart['beats'])->map(function (array $linea, string $clave) use ($soloPublicados) {
            $query = Beat::with('usuario')->whereKey($linea['id']);
            if ($soloPublicados) {
                $query->publicados();
            }

            $beat = $query->first();
            $licencia = Licencia::whereIn('tipo_licencia', LicenciaCompra::tiposPermitidos())
                ->find($linea['licencia_id']);

            if (!$beat || !$licencia) {
                return null;
            }

            return self::linea('beat', $clave, $beat, $licencia, (float) $beat->precio_base_licencia, $beat->titulo_beat);
        })->filter()->values();

        $colecciones = collect($cart['colecciones'])->map(function (array $linea, string $clave) use ($soloPublicados) {
            $query = Coleccion::with(['usuario', 'beats'])->whereKey($linea['id']);
            if ($soloPublicados) {
                $query->publicadas();
            }

            $coleccion = $query->first();
            $licencia = Licencia::whereIn('tipo_licencia', LicenciaCompra::tiposPermitidos())
                ->find($linea['licencia_id']);

            if (!$coleccion || !$licencia) {
                return null;
            }

            return self::linea('coleccion', $clave, $coleccion, $licencia, (float) $coleccion->precio, $coleccion->titulo_coleccion);
        })->filter()->values();

        $servicios = collect($cart['servicios'])->map(function (array $linea, string $clave) {
            $proyecto = Proyecto::with(['servicio.usuario'])
                ->whereKey($linea['proyecto_id'])
                ->where('id_servicio', $linea['id'])
                ->first();

            if (!$proyecto || !$proyecto->servicio) {
                return null;
            }

            $servicio = $proyecto->servicio;

            return [
                'tipo' => 'servicio',
                'clave' => $clave,
                'producto' => $servicio,
                'proyecto' => $proyecto,
                'precio_base' => (float) $servicio->precio_servicio,
                'precio_licencia' => 0.0,
                'precio_final' => round((float) $servicio->precio_servicio, 2),
                'nombre_producto' => $servicio->titulo_servicio,
            ];
        })->filter()->values();

        $planes = collect($cart['planes'])->map(function (array $linea, string $clave) {
            $planRol = PlanPorRol::with(['plan', 'rol'])->whereKey($linea['id'])->first();

            if (!$planRol || !$planRol->plan || !$planRol->rol || !in_array($planRol->rol->nombre_rol, ['productor', 'ingeniero'], true)) {
                return null;
            }

            return [
                'tipo' => 'plan',
                'clave' => $clave,
                'producto' => $planRol,
                'plan' => $planRol->plan,
                'rol' => $planRol->rol,
                'precio_base' => (float) $planRol->plan->precio_mensual,
                'precio_licencia' => 0.0,
                'precio_final' => round((float) $planRol->plan->precio_mensual, 2),
                'nombre_producto' => $planRol->plan->nombre_plan,
            ];
        })->filter()->values();

        return [
            'beats' => $beats,
            'colecciones' => $colecciones,
            'servicios' => $servicios,
            'planes' => $planes,
            'total' => round($beats->sum('precio_final') + $colecciones->sum('precio_final') + $servicios->sum('precio_final') + $planes->sum('precio_final'), 2),
        ];
    }

    private static function linea(string $tipo, string $clave, object $producto, Licencia $licencia, float $precioBase, string $nombre): array
    {
        $precioLicencia = LicenciaCompra::precio($licencia);
        $spec = LicenciaCompra::spec($licencia);

        return [
            'tipo' => $tipo,
            'clave' => $clave,
            'producto' => $producto,
            'licencia' => $licencia,
            'spec' => $spec,
            'precio_base' => $precioBase,
            'precio_licencia' => $precioLicencia,
            'precio_final' => round($precioBase + $precioLicencia, 2),
            'nombre_producto' => $nombre,
            'exclusiva_vendida' => LicenciaCompra::exclusivaVendida($tipo, $producto->id),
        ];
    }
}
