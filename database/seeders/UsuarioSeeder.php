<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Siembra los usuarios de demostración de LevelBeat.
 *
 * CAMBIOS respecto al esquema antiguo:
 * - Se elimina la columna 'rol' de los inserts (ya no existe en 'usuario').
 * - Los roles se asignan via la tabla pivote 'usuario_rol'.
 * - Este seeder debe ejecutarse DESPUÉS de RolSeeder.
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Insertar usuarios (sin columna 'rol') ──────────────────────────
        $usuarios = [
            [
                'nombre_usuario' => 'Admin LevelBeat',
                'direccion_correo' => 'admin@levelbeats.com',
                'contrasena' => Hash::make('admin123'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'Administrador de la plataforma',
                'calle' => null,
                'localidad' => 'Madrid',
                'provincia' => 'Madrid',
                'pais' => 'España',
                'codigo_postal' => '28013',
                'fecha_registro' => now(),
            ],
            [
                'nombre_usuario' => 'Carlos Beatmaker',
                'direccion_correo' => 'carlos@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'Productor especializado en trap latino',
                'calle' => 'Calle Gran Vía 12',
                'localidad' => 'Madrid',
                'provincia' => 'Madrid',
                'pais' => 'España',
                'codigo_postal' => '28013',
                'fecha_registro' => now(),
            ],
            [
                'nombre_usuario' => 'Lucía Flow',
                'direccion_correo' => 'lucia@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'Cantante urbana y compositora',
                'calle' => 'Avenida Diagonal 45',
                'localidad' => 'Barcelona',
                'provincia' => 'Barcelona',
                'pais' => 'España',
                'codigo_postal' => '08019',
                'fecha_registro' => now(),
            ],
            [
                'nombre_usuario' => 'Miguel Producer',
                'direccion_correo' => 'miguel@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'Productor de reggaeton y afrobeat',
                'calle' => 'Calle Feria 8',
                'localidad' => 'Sevilla',
                'provincia' => 'Sevilla',
                'pais' => 'España',
                'codigo_postal' => '41003',
                'fecha_registro' => now(),
            ],
            [
                'nombre_usuario' => 'Andrea Music',
                'direccion_correo' => 'andrea@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'Artista emergente de R&B',
                'calle' => 'Calle Colón 21',
                'localidad' => 'Valencia',
                'provincia' => 'Valencia',
                'pais' => 'España',
                'codigo_postal' => '46004',
                'fecha_registro' => now(),
            ],
            [
                'nombre_usuario' => 'DJ Nova',
                'direccion_correo' => 'nova@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'verificacion_completada' => true,
                'descripcion_perfil' => 'DJ profesional y creador de remixes',
                'calle' => 'Calle Larios 7',
                'localidad' => 'Málaga',
                'provincia' => 'Málaga',
                'pais' => 'España',
                'codigo_postal' => '29005',
                'fecha_registro' => now(),
            ],
        ];

        foreach ($usuarios as $userData) {
            DB::table('usuario')->updateOrInsert(
                ['direccion_correo' => $userData['direccion_correo']],
                $userData
            );
        }

        // ── 2. Recuperar IDs de roles del catálogo ───────────────────────────
        $idAdmin = DB::table('rol')->where('nombre_rol', 'admin')->value('id');
        $idUsuario = DB::table('rol')->where('nombre_rol', 'usuario')->value('id');
        $idProductor = DB::table('rol')->where('nombre_rol', 'productor')->value('id');
        $idIngeniero = DB::table('rol')->where('nombre_rol', 'ingeniero')->value('id');

        if (!$idAdmin || !$idUsuario) {
            $this->command->warn('⚠ No se encontraron roles en la tabla rol. Ejecuta RolSeeder primero.');
            return;
        }

        // ── 3. Recuperar IDs de usuarios recién insertados ───────────────────
        $correos = [
            'admin@levelbeats.com',
            'carlos@levelbeats.com',
            'lucia@levelbeats.com',
            'miguel@levelbeats.com',
            'andrea@levelbeats.com',
            'nova@levelbeats.com',
        ];

        $usuarios = DB::table('usuario')
            ->whereIn('direccion_correo', $correos)
            ->pluck('id', 'direccion_correo'); // ['correo' => id]

        // ── 4. Asignar roles en usuario_rol ──────────────────────────────────
        $asignaciones = [
            // Admin: rol admin
            'admin@levelbeats.com' => [$idAdmin],

            // Productores: rol usuario + productor
            'carlos@levelbeats.com' => [$idUsuario, $idProductor],
            'miguel@levelbeats.com' => [$idUsuario, $idProductor],

            // Usuarios estándar: solo rol usuario
            'lucia@levelbeats.com' => [$idUsuario],
            'andrea@levelbeats.com' => [$idUsuario],

            // Ingeniero: rol usuario + ingeniero
            'nova@levelbeats.com' => [$idUsuario, $idIngeniero],
        ];

        foreach ($asignaciones as $correo => $roles) {
            $idUsuarioFila = $usuarios[$correo] ?? null;
            if (!$idUsuarioFila)
                continue;

            foreach ($roles as $idRol) {
                DB::table('usuario_rol')->insertOrIgnore([
                    'id_usuario' => $idUsuarioFila,
                    'id_rol' => $idRol,
                    'rol_activo' => 1,
                    'fecha_alta_rol' => now(),
                ]);
            }
        }

        $this->command->info('✔ Usuarios sembrados con sus roles asignados en usuario_rol.');
    }
}

