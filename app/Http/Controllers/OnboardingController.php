<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\PlanPorRol;
use App\Models\Suscripcion;

/**
 * Controlador del flujo inicial de elección de rol y plan.
 *
 * Se utiliza después del registro para decidir si el usuario permanece como
 * cliente básico o activa un rol profesional de productor/ingeniero mediante
 * la selección de un plan asociado.
 */
class OnboardingController extends Controller
{
    /**
     * Muestra las opciones iniciales de rol disponibles para un usuario nuevo.
     */
    public function showRoles()
    {
        return view('auth.onboarding.roles');
    }

    /**
     * Procesa la elección de rol y redirige a planes si se elige un rol profesional.
     */
    public function setRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:cliente,productor,ingeniero'
        ]);

        $user = auth()->user();

        if ($request->role === 'productor' || $request->role === 'ingeniero') {
            return redirect()->route('onboarding.planes', ['rol' => $request->role]);
        }

        // Si elige "cliente", purgamos roles pro y anulamos la suscripción actual (Downgrade B2B a B2C)
        $profRoles = Rol::whereIn('nombre_rol', ['productor', 'ingeniero'])->pluck('id')->toArray();
        $user->roles()->detach($profRoles);
        
        Suscripcion::where('id_usuario', $user->id)
            ->where('estado_suscripcion', 'activa')
            ->update([
                'estado_suscripcion' => 'cancelada',
                'fecha_fin' => now(),
                'renovacion_auto' => 0,
            ]);

        return redirect()->route('beat.index')->with('status', 'Perfil adaptado al modo Cliente Comprador.');
    }

    /**
     * Muestra los planes configurados para productor o ingeniero.
     */
    public function showPlanes($rolName)
    {
        if (!in_array($rolName, ['productor', 'ingeniero'])) {
            return redirect()->route('beat.index');
        }

        $rol = Rol::where('nombre_rol', $rolName)->firstOrFail();
        
        // Cargar los planes asociados a este rol específico cruzando con la tabla padre Plan
        $planesPorRol = PlanPorRol::with('plan')->where('id_rol', $rol->id)->get();

        // Autocorrección de Hueco de MVP: Si la base de datos está vacía, Inyectamos y releemos en tiempo real
        if ($planesPorRol->isEmpty()) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'PlanesSeeder']);
            $planesPorRol = PlanPorRol::with('plan')->where('id_rol', $rol->id)->get();
        }

        return view('auth.onboarding.planes', compact('planesPorRol', 'rol', 'rolName'));
    }

    /**
     * Valida el plan seleccionado y delega el alta al flujo central de gestión de planes.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'id_plan_rol' => 'required|integer',
            'id_rol'      => 'required|integer'
        ]);

        $planRol = PlanPorRol::with('rol')->findOrFail($request->id_plan_rol);

        if ((int) $planRol->id_rol !== (int) $request->id_rol) {
            return back()->with('status', 'El plan seleccionado no corresponde al rol indicado.');
        }

        $rol = $planRol->rol;
        if (!$rol || !in_array($rol->nombre_rol, ['productor', 'ingeniero'], true)) {
            return back()->with('status', 'El rol seleccionado no es válido para activar un plan profesional.');
        }

        return redirect()->route('usuario.plan.checkout', $planRol)
            ->with('status', 'Revisa el resumen del plan antes de confirmar el alta.');
    }
}
