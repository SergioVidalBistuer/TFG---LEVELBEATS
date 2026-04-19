<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Puebla el catálogo base de roles de LevelBeat.
 *
 * Debe ejecutarse ANTES de UsuarioSeeder para que los id de rol
 * existan cuando se intente insertar en usuario_rol.
 *
 * Usa insertOrIgnore para ser idempotente: se puede re-ejecutar
 * sin duplicar ni romper nada.
 */
class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // Rol de administrador de la plataforma
            ['nombre_rol' => 'admin'],

            // Rol base para cualquier usuario registrado (comprador / oyente)
            ['nombre_rol' => 'usuario'],

            // Productor que publica beats y colecciones
            ['nombre_rol' => 'productor'],

            // Ingeniero de sonido que ofrece servicios de mezcla / master
            ['nombre_rol' => 'ingeniero'],
        ];

        foreach ($roles as $rol) {
            DB::table('rol')->insertOrIgnore($rol);
        }

        $this->command->info('✔ Roles base insertados: admin, usuario, productor, ingeniero');
    }
}
