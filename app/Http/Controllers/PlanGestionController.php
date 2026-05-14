<?php

namespace App\Http\Controllers;

use App\Models\PlanPorRol;
use App\Models\Rol;
use App\Models\Suscripcion;
use App\Support\CarritoCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanGestionController extends Controller
{
    private const ROLES_PROFESIONALES = ['productor', 'ingeniero'];

    public function index()
    {
        $usuario = auth()->user();

        $usuario->load([
            'roles',
            'suscripciones.planPorRol.plan',
            'suscripciones.planPorRol.rol',
        ]);

        $roles = Rol::whereIn('nombre_rol', array_merge(['usuario'], self::ROLES_PROFESIONALES))
            ->get()
            ->keyBy('nombre_rol');

        $rolesActivos = $usuario->roles()
            ->where('usuario_rol.rol_activo', 1)
            ->pluck('nombre_rol')
            ->map(fn ($rol) => strtolower($rol))
            ->values();

        $suscripcionesPorRol = $usuario->suscripciones
            ->filter(fn ($suscripcion) => $suscripcion->planPorRol?->rol)
            ->sortByDesc('fecha_inicio')
            ->groupBy(fn ($suscripcion) => $suscripcion->planPorRol->rol->nombre_rol);

        $planesPorRol = PlanPorRol::with(['plan', 'rol'])
            ->whereHas('rol', fn ($query) => $query->whereIn('nombre_rol', self::ROLES_PROFESIONALES))
            ->get()
            ->groupBy(fn ($planRol) => $planRol->rol->nombre_rol);

        return view('usuario.plan.index', compact(
            'usuario',
            'roles',
            'rolesActivos',
            'suscripcionesPorRol',
            'planesPorRol'
        ));
    }

    public function activarRol(Request $request)
    {
        $datos = $request->validate([
            'rol' => 'required|in:productor,ingeniero',
        ]);

        return redirect()->route('onboarding.planes', ['rol' => $datos['rol']])
            ->with('status', 'Elige el plan que quieres activar. El rol se activará al confirmar el alta.');
    }

    public function cancelarRol(Request $request)
    {
        $datos = $request->validate([
            'rol' => 'required|in:productor,ingeniero',
        ]);

        $usuario = auth()->user();
        $rol = Rol::where('nombre_rol', $datos['rol'])->firstOrFail();

        Suscripcion::where('id_usuario', $usuario->id)
            ->where('id_rol', $rol->id)
            ->where('estado_suscripcion', 'activa')
            ->update([
                'estado_suscripcion' => 'cancelada',
                'fecha_fin' => now(),
                'renovacion_auto' => 0,
            ]);

        $usuario->roles()->updateExistingPivot($rol->id, ['rol_activo' => 0]);

        return redirect()->route('usuario.plan.index')
            ->with('status', 'Rol profesional cancelado. Tu histórico se conserva intacto.');
    }

    public function checkout(PlanPorRol $planRol)
    {
        $planRol->load(['plan', 'rol']);
        $this->validarPlanProfesional($planRol);

        $usuario = auth()->user();
        $suscripcionActual = Suscripcion::with('planPorRol.plan')
            ->where('id_usuario', $usuario->id)
            ->where('id_rol', $planRol->id_rol)
            ->where('estado_suscripcion', 'activa')
            ->latest('fecha_inicio')
            ->first();

        if ($suscripcionActual && (int) $suscripcionActual->id_plan_rol === (int) $planRol->id) {
            return redirect()->route('usuario.plan.index')
                ->with('status', 'Ese plan ya está activo en tu cuenta.');
        }

        $precio = (float) ($planRol->plan->precio_mensual ?? 0);

        if ($precio <= 0) {
            DB::transaction(fn () => $this->activarSuscripcion($planRol));

            return redirect()->route('usuario.plan.index')
                ->with('status', 'Plan gratuito activado correctamente.');
        }

        session()->put('cart', CarritoCompra::agregarPlan(CarritoCompra::vacio(), $planRol->id));

        return redirect()->route('compra.checkout.show')
            ->with('status', 'Revisa el plan seleccionado y continúa con el pago.');
    }

    public function confirmarPago(Request $request, PlanPorRol $planRol)
    {
        return redirect()->route('usuario.plan.checkout', $planRol);
    }

    private function validarPlanProfesional(PlanPorRol $planRol): void
    {
        if (!$planRol->rol || !in_array($planRol->rol->nombre_rol, self::ROLES_PROFESIONALES, true)) {
            abort(404);
        }
    }

    private function activarSuscripcion(PlanPorRol $planRol): void
    {
        $usuario = auth()->user();
        $rol = $planRol->rol;

        Suscripcion::where('id_usuario', $usuario->id)
            ->where('id_rol', $rol->id)
            ->where('estado_suscripcion', 'activa')
            ->update([
                'estado_suscripcion' => 'cancelada',
                'fecha_fin' => now(),
                'renovacion_auto' => 0,
            ]);

        Suscripcion::create([
            'id_usuario' => $usuario->id,
            'id_plan_rol' => $planRol->id,
            'id_rol' => $rol->id,
            'estado_suscripcion' => 'activa',
            'fecha_inicio' => now(),
            'fecha_fin' => null,
            'renovacion_auto' => 1,
            'tipo_pago' => 'mensual',
        ]);

        $usuario->roles()->syncWithoutDetaching([
            $rol->id => ['rol_activo' => 1, 'fecha_alta_rol' => now()],
        ]);
        $usuario->roles()->updateExistingPivot($rol->id, ['rol_activo' => 1]);
    }

}
