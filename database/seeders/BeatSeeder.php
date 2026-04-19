<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeatSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('beat_licencia')->truncate();
        DB::table('beat')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $idProductor = DB::table('usuario')->where('direccion_correo', 'carlos@levelbeats.com')->value('id');
        if (!$idProductor) return;

        DB::table('beat')->insert([
            [
                'id_usuario' => $idProductor,
                'titulo_beat' => 'Dark Trap Instrumental',
                'genero_musical' => 'Trap',
                'tempo_bpm' => 120,
                'tono_musical' => 'C#',
                'estado_de_animo' => 'Oscuro',
                'precio_base_licencia' => 19.99,
                'activo_publicado' => 1,
                'fecha_publicacion' => now(),
                'url_portada_beat' => 'media/img/nocheDeAmor.jpg',
                'url_audio_previsualizacion' => 'media/audio/demo.mp3'
            ],
            [
                'id_usuario' => $idProductor,
                'titulo_beat' => 'Summer Reggaeton',
                'genero_musical' => 'Reggaeton',
                'tempo_bpm' => 95,
                'tono_musical' => 'F',
                'estado_de_animo' => 'Alegre',
                'precio_base_licencia' => 24.99,
                'activo_publicado' => 1,
                'fecha_publicacion' => now(),
                'url_portada_beat' => 'media/img/nocheDeAmor.jpg',
                'url_audio_previsualizacion' => 'media/audio/demo.mp3'
            ]
        ]);

        $beats = DB::table('beat')->pluck('id');
        $licencias = DB::table('licencia')->pluck('id');
        
        foreach($beats as $beatId) {
            foreach($licencias as $licenciaId) {
                DB::table('beat_licencia')->insert([
                    'id_beat' => $beatId,
                    'id_licencia' => $licenciaId
                ]);
            }
        }
    }
}
