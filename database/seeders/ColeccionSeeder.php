<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColeccionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('coleccion_beat')->truncate();
        DB::table('coleccion')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $idProductor = DB::table('usuario as u')
            ->join('usuario_rol as ur', 'ur.id_usuario', '=', 'u.id')
            ->join('rol as r', 'r.id', '=', 'ur.id_rol')
            ->where('r.nombre_rol', 'productor')
            ->where('ur.rol_activo', 1)
            ->orderBy('u.id')
            ->value('u.id');

        if (!$idProductor) {
            throw new \Exception("No existe un usuario con rol 'productor' activo. Ejecuta UsuarioRolSeeder antes.");
        }
        
        $fechaCreacion = now();

        $colecciones = [
            [
                'id_usuario' => $idProductor,
                'titulo_coleccion' => 'Melodic Trap Essentials',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Seleccion de beats melodic trap.',
                'estilo_genero' => 'Melodic Trap',
                'es_destacada' => 1,
                'fecha_creacion' => $fechaCreacion,
            ],
            [
                'id_usuario' => $idProductor,
                'titulo_coleccion' => 'UK Drill Pack',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Seleccion de beats UK Drill.',
                'estilo_genero' => 'UK Drill',
                'es_destacada' => 1,
                'fecha_creacion' => $fechaCreacion,
            ],
            [
                'id_usuario' => $idProductor,
                'titulo_coleccion' => 'Club / Deep House',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Seleccion de beats club y deep house.',
                'estilo_genero' => 'Club / Deep House',
                'es_destacada' => 1,
                'fecha_creacion' => $fechaCreacion,
            ],
            [
                'id_usuario' => $idProductor,
                'titulo_coleccion' => 'Trap Hits',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Seleccion de beats trap.',
                'estilo_genero' => 'Trap',
                'es_destacada' => 1,
                'fecha_creacion' => $fechaCreacion,
            ],
            [
                'id_usuario' => $idProductor,
                'titulo_coleccion' => 'Piano / Sad Pack',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Seleccion de beats piano y sad.',
                'estilo_genero' => 'Piano / Sad',
                'es_destacada' => 1,
                'fecha_creacion' => $fechaCreacion,
            ],
        ];

        DB::table('coleccion')->insert($colecciones);
    }
}
