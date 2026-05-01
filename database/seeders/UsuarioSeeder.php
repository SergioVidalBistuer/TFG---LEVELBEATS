<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Siembra los usuarios de demostración de LevelBeat.
 *
 * Solo inserta filas en la tabla `usuario`.
 * La asignación de roles (tabla usuario_rol) queda en UsuarioRolSeeder.
 *
 * Perfiles creados:
 *  - 1 admin          → admin@levelbeats.com
 *  - 2 productores    → carlos@levelbeats.com  |  miguel@levelbeats.com
 *  - 1 ingeniero      → nova@levelbeats.com
 *  - 2 usuarios/clientes → lucia@levelbeats.com  |  andrea@levelbeats.com
 *
 * Contraseña de prueba para todos los perfiles no-admin: Password123!
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            // ─── ADMIN ────────────────────────────────────────────────────────
            [
                'nombre_usuario'          => 'Admin LevelBeat',
                'direccion_correo'        => 'admin@levelbeats.com',
                'contrasena'              => Hash::make('AdminSecure#2026'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Administrador principal de la plataforma LevelBeat.',
                'calle'                   => null,
                'localidad'               => 'Madrid',
                'provincia'               => 'Madrid',
                'pais'                    => 'España',
                'codigo_postal'           => '28013',
                'fecha_registro'          => now(),
            ],

            // ─── PRODUCTOR 1 ──────────────────────────────────────────────────
            [
                'nombre_usuario'          => 'Carlos Beatmaker',
                'direccion_correo'        => 'carlos@levelbeats.com',
                'contrasena'              => Hash::make('Password123!'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Productor especializado en trap latino y drill.',
                'calle'                   => 'Calle Gran Vía 12',
                'localidad'               => 'Madrid',
                'provincia'               => 'Madrid',
                'pais'                    => 'España',
                'codigo_postal'           => '28013',
                'fecha_registro'          => now(),
            ],

            // ─── PRODUCTOR 2 ──────────────────────────────────────────────────
            [
                'nombre_usuario'          => 'Miguel Producer',
                'direccion_correo'        => 'miguel@levelbeats.com',
                'contrasena'              => Hash::make('Password123!'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Productor de reggaeton, afrobeat y R&B.',
                'calle'                   => 'Calle Feria 8',
                'localidad'               => 'Sevilla',
                'provincia'               => 'Sevilla',
                'pais'                    => 'España',
                'codigo_postal'           => '41003',
                'fecha_registro'          => now(),
            ],

            // ─── INGENIERO ────────────────────────────────────────────────────
            [
                'nombre_usuario'          => 'DJ Nova',
                'direccion_correo'        => 'nova@levelbeats.com',
                'contrasena'              => Hash::make('Password123!'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Ingeniero de mezcla y masterización. DJ profesional.',
                'calle'                   => 'Calle Larios 7',
                'localidad'               => 'Málaga',
                'provincia'               => 'Málaga',
                'pais'                    => 'España',
                'codigo_postal'           => '29005',
                'fecha_registro'          => now(),
            ],

            // ─── USUARIO / CLIENTE 1 ──────────────────────────────────────────
            [
                'nombre_usuario'          => 'Lucía Flow',
                'direccion_correo'        => 'lucia@levelbeats.com',
                'contrasena'              => Hash::make('Password123!'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Cantante urbana y compositora emergente.',
                'calle'                   => 'Avenida Diagonal 45',
                'localidad'               => 'Barcelona',
                'provincia'               => 'Barcelona',
                'pais'                    => 'España',
                'codigo_postal'           => '08019',
                'fecha_registro'          => now(),
            ],

            // ─── USUARIO / CLIENTE 2 ──────────────────────────────────────────
            [
                'nombre_usuario'          => 'Andrea Music',
                'direccion_correo'        => 'andrea@levelbeats.com',
                'contrasena'              => Hash::make('Password123!'),
                'verificacion_completada' => true,
                'descripcion_perfil'      => 'Artista independiente de R&B y pop urbano.',
                'calle'                   => 'Calle Colón 21',
                'localidad'               => 'Valencia',
                'provincia'               => 'Valencia',
                'pais'                    => 'España',
                'codigo_postal'           => '46004',
                'fecha_registro'          => now(),
            ],
        ];

        foreach ($usuarios as $datos) {
            DB::table('usuario')->updateOrInsert(
                ['direccion_correo' => $datos['direccion_correo']],
                $datos
            );
        }

        $this->command->info('✔ UsuarioSeeder: 6 usuarios insertados/actualizados.');
    }
}
