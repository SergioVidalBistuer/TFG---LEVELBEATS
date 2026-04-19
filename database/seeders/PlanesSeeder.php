<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Rol;
use App\Models\PlanPorRol;

class PlanesSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Planes Base en la plataforma
        $planGratis = Plan::firstOrCreate(
            ['nombre_plan' => 'Inicio'],
            ['precio_mensual' => 0.00, 'beneficios_generales' => 'Ruta básica para probar el mercado']
        );

        $planPro = Plan::firstOrCreate(
            ['nombre_plan' => 'Nivel Pro'],
            ['precio_mensual' => 9.99, 'beneficios_generales' => 'Da el salto profesional con mayor alcance']
        );

        $planPremium = Plan::firstOrCreate(
            ['nombre_plan' => 'Máster Premium'],
            ['precio_mensual' => 24.99, 'beneficios_generales' => 'Maximiza ganancias y tu capacidad cloud']
        );

        // 2. Traer Roles técnicos desde la base
        $rolProductor = Rol::where('nombre_rol', 'productor')->first();
        $rolIngeniero = Rol::where('nombre_rol', 'ingeniero')->first();

        if (!$rolProductor || !$rolIngeniero) {
            return; // Precaución si no existen los roles
        }

        // 3. Vincular configuraciones de Paises al rol PRODUCTOR
        PlanPorRol::firstOrCreate([
            'id_plan' => $planGratis->id,
            'id_rol' => $rolProductor->id
        ], [
            'beats_publicables_mes' => 3,
            'almacenamiento_gigabytes' => 2,
            'encargos_max_ingeniero' => 0,
            'prioridad_soporte' => 'basica'
        ]);

        PlanPorRol::firstOrCreate([
            'id_plan' => $planPro->id,
            'id_rol' => $rolProductor->id
        ], [
            'beats_publicables_mes' => 15,
            'almacenamiento_gigabytes' => 10,
            'encargos_max_ingeniero' => 0,
            'prioridad_soporte' => 'prioritaria'
        ]);

        PlanPorRol::firstOrCreate([
            'id_plan' => $planPremium->id,
            'id_rol' => $rolProductor->id
        ], [
            'beats_publicables_mes' => 99, // Alto tope
            'almacenamiento_gigabytes' => 50,
            'encargos_max_ingeniero' => 0,
            'prioridad_soporte' => 'premium'
        ]);

        // 4. Vincular Planes al INGENIERO (sin beats, basado en capacidad de encargos)
        PlanPorRol::firstOrCreate([
            'id_plan' => $planGratis->id,
            'id_rol' => $rolIngeniero->id
        ], [
            'beats_publicables_mes' => 0,
            'almacenamiento_gigabytes' => 5,
            'encargos_max_ingeniero' => 3,
            'prioridad_soporte' => 'basica'
        ]);

        PlanPorRol::firstOrCreate([
            'id_plan' => $planPro->id,
            'id_rol' => $rolIngeniero->id
        ], [
            'beats_publicables_mes' => 0,
            'almacenamiento_gigabytes' => 20,
            'encargos_max_ingeniero' => 15,
            'prioridad_soporte' => 'prioritaria'
        ]);

        PlanPorRol::firstOrCreate([
            'id_plan' => $planPremium->id,
            'id_rol' => $rolIngeniero->id
        ], [
            'beats_publicables_mes' => 0,
            'almacenamiento_gigabytes' => 100,
            'encargos_max_ingeniero' => 99, // Alto tope
            'prioridad_soporte' => 'premium'
        ]);
    }
}
