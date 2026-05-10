<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenciaSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('licencia')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('licencia')->insert([
            [
                'tipo_licencia' => 'basica',
                'nombre_licencia' => 'Licencia Básica no exclusiva',
                'descripcion_licencia' => 'MP3, 50.000 reproducciones, 3.000 copias, 1 videoclip y 5 actuaciones.',
                'duracion_meses' => 24,
                'reproducciones_max' => 50000,
                'autoriza_monetizacion' => 1,
                'formato_audio' => 'MP3',
                'precio_licencia' => 29.99
            ],
            [
                'tipo_licencia' => 'premium',
                'nombre_licencia' => 'Licencia Premium no exclusiva',
                'descripcion_licencia' => 'MP3 + WAV, 250.000 reproducciones, 10.000 copias, 2 videoclips y 25 actuaciones.',
                'duracion_meses' => 48,
                'reproducciones_max' => 250000,
                'autoriza_monetizacion' => 1,
                'formato_audio' => 'MP3 + WAV',
                'precio_licencia' => 59.99
            ],
            [
                'tipo_licencia' => 'exclusiva',
                'nombre_licencia' => 'Licencia Exclusiva',
                'descripcion_licencia' => 'MP3 + WAV + STEMS, derechos exclusivos y reproducción ilimitada.',
                'duracion_meses' => null,
                'reproducciones_max' => null,
                'autoriza_monetizacion' => 1,
                'formato_audio' => 'MP3 + WAV + STEMS',
                'precio_licencia' => 199.99
            ]
        ]);
    }
}
