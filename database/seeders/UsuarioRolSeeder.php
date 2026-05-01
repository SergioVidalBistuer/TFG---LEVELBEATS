<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Asigna roles a los usuarios usando la tabla pivote `usuario_rol`.
 *
 * Dependencias (deben ejecutarse antes):
 *   - RolSeeder     → garantiza que los roles existan en la tabla `rol`
 *   - UsuarioSeeder → garantiza que los usuarios existan en la tabla `usuario`
 *
 * Diseño de roles:
 *   - admin@levelbeats.com   → [admin]
 *   - carlos@levelbeats.com  → [usuario, productor]
 *   - miguel@levelbeats.com  → [usuario, productor]
 *   - nova@levelbeats.com    → [usuario, ingeniero]
 *   - lucia@levelbeats.com   → [usuario]
 *   - andrea@levelbeats.com  → [usuario]
 *
 * Usa insertOrIgnore para ser idempotente (re-ejecutable sin duplicados).
 */
class UsuarioRolSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Leer IDs de roles del catálogo ────────────────────────────────
        $roles = DB::table('rol')
            ->whereIn('nombre_rol', ['admin', 'usuario', 'productor', 'ingeniero'])
            ->pluck('id', 'nombre_rol'); // ['admin' => 1, 'usuario' => 2, ...]

        $rolesRequeridos = ['admin', 'usuario', 'productor', 'ingeniero'];
        foreach ($rolesRequeridos as $nombre) {
            if (empty($roles[$nombre])) {
                $this->command->error("✘ Rol '{$nombre}' no encontrado. Ejecuta RolSeeder primero.");
                return;
            }
        }

        // ── 2. Leer IDs de los usuarios por correo ───────────────────────────
        $correos = [
            'admin@levelbeats.com',
            'carlos@levelbeats.com',
            'miguel@levelbeats.com',
            'nova@levelbeats.com',
            'lucia@levelbeats.com',
            'andrea@levelbeats.com',
        ];

        $usuarios = DB::table('usuario')
            ->whereIn('direccion_correo', $correos)
            ->pluck('id', 'direccion_correo'); // ['correo' => id]

        foreach ($correos as $correo) {
            if (empty($usuarios[$correo])) {
                $this->command->warn("⚠ Usuario '{$correo}' no encontrado. Asegúrate de ejecutar UsuarioSeeder primero.");
            }
        }

        // ── 3. Mapa de asignaciones correo → [roles] ─────────────────────────
        $asignaciones = [
            'admin@levelbeats.com'  => ['admin'],
            'carlos@levelbeats.com' => ['usuario', 'productor'],
            'miguel@levelbeats.com' => ['usuario', 'productor'],
            'nova@levelbeats.com'   => ['usuario', 'ingeniero'],
            'lucia@levelbeats.com'  => ['usuario'],
            'andrea@levelbeats.com' => ['usuario'],
        ];

        // ── 4. Insertar en usuario_rol ────────────────────────────────────────
        $insertados = 0;

        foreach ($asignaciones as $correo => $nombresRol) {
            $idUsuario = $usuarios[$correo] ?? null;
            if (!$idUsuario) {
                continue;
            }

            foreach ($nombresRol as $nombreRol) {
                $idRol = $roles[$nombreRol] ?? null;
                if (!$idRol) {
                    continue;
                }

                DB::table('usuario_rol')->insertOrIgnore([
                    'id_usuario'    => $idUsuario,
                    'id_rol'        => $idRol,
                    'rol_activo'    => 1,
                    'fecha_alta_rol' => now(),
                ]);

                $insertados++;
            }
        }

        $this->command->info("✔ UsuarioRolSeeder: {$insertados} asignaciones de rol insertadas en usuario_rol.");
    }
}
