<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColeccionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('coleccion_beat')->truncate();
        DB::table('coleccion')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $idProductor = DB::table('usuario')->where('direccion_correo', 'carlos@levelbeats.com')->value('id');
        if (!$idProductor) return;

        $idColeccion = DB::table('coleccion')->insertGetId([
            'id_usuario' => $idProductor,
            'titulo_coleccion' => 'Hits 2024',
            'tipo_coleccion' => 'publica',
            'descripcion_coleccion' => 'Mejores instrumentales del año',
            'estilo_genero' => 'Urbano',
            'es_destacada' => 1,
            'fecha_creacion' => now(),
        ]);

        $idBeat = DB::table('beat')->where('id_usuario', $idProductor)->value('id');
        if ($idBeat) {
            DB::table('coleccion_beat')->insert([
                'id_coleccion' => $idColeccion,
                'id_beat' => $idBeat
            ]);
        }
    }
}
