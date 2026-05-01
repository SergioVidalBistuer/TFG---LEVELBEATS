<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\PlanPorRol;
use App\Models\Suscripcion;

class OnboardingController extends Controller
{
    public function showRoles()
    {
        return view('auth.onboarding.roles');
    }

    public function setRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:cliente,productor,ingeniero'
        ]);

        $user = auth()->user();

        if ($request->role === 'productor' || $request->role === 'ingeniero') {
            $rol = Rol::where('nombre_rol', $request->role)->first();
            if ($rol && !$user->tieneRol($request->role)) {
                $user->roles()->attach($rol->id, ['rol_activo' => 1]);
            }
            return redirect()->route('onboarding.planes', ['rol' => $request->role]);
        }

        // Si elige "cliente", purgamos roles pro y anulamos la suscripción actual (Downgrade B2B a B2C)
        $profRoles = Rol::whereIn('nombre_rol', ['productor', 'ingeniero'])->pluck('id')->toArray();
        $user->roles()->detach($profRoles);
        
        Suscripcion::where('id_usuario', $user->id)
            ->where('estado_suscripcion', 'activa')
            ->update(['estado_suscripcion' => 'expirada']);

        return redirect()->route('beat.index')->with('status', 'Perfil adaptado al modo Cliente Comprador.');
    }

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

    public function subscribe(Request $request)
    {
        $request->validate([
            'id_plan_rol' => 'required|integer',
            'id_rol'      => 'required|integer'
        ]);

        $planRol = PlanPorRol::findOrFail($request->id_plan_rol);

        // Política de Exclusividad por Rol:
        // Caducar cualquier suscripción activa PREVIA de este MISMO ROL para evitar duplicados,
        // respetando si el usuario ya tiene otro negocio (ej. Productor) activo.
        Suscripcion::where('id_usuario', auth()->id())
            ->where('id_rol', $request->id_rol)
            ->where('estado_suscripcion', 'activa')
            ->update(['estado_suscripcion' => 'expirada']);

        Suscripcion::create([
            'id_usuario'         => auth()->id(),
            'id_plan_rol'        => $planRol->id,
            'id_rol'             => $request->id_rol,
            'estado_suscripcion' => 'activa',
            'fecha_inicio'       => now(),
            'fecha_fin'          => null,
            'renovacion_auto'    => 1,
            'tipo_pago'          => 'mensual'
        ]);

        return redirect()->route('beat.index')->with('status', '¡Suscripción activada! Bienvenido a tu panel profesional.');
    }
}
