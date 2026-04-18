<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuario')->insert([
            [
                'nombre_usuario' => 'Carlos Beatmaker',
                'direccion_correo' => 'carlos@levelbeats.com',
                'contrasena' => Hash::make('123456'),
                'rol' => 'usuario',
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
                'rol' => 'usuario',
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
                'rol' => 'usuario',
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
                'rol' => 'usuario',
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
                'rol' => 'usuario',
                'verificacion_completada' => true,
                'descripcion_perfil' => 'DJ profesional y creador de remixes',
                'calle' => 'Calle Larios 7',
                'localidad' => 'Málaga',
                'provincia' => 'Málaga',
                'pais' => 'España',
                'codigo_postal' => '29005',
                'fecha_registro' => now(),
            ],
        ]);
    }
}
