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
                'nombre_licencia' => 'MP3 Lease',
                'descripcion_licencia' => 'Uso básico para plataformas sociales.',
                'duracion_meses' => 24,
                'reproducciones_max' => 10000,
                'autoriza_monetizacion' => 0,
                'formato_audio' => 'MP3',
                'precio_licencia' => 19.99
            ],
            [
                'tipo_licencia' => 'premium',
                'nombre_licencia' => 'WAV Lease',
                'descripcion_licencia' => 'Uso premium para plataformas profesionales.',
                'duracion_meses' => 48,
                'reproducciones_max' => 500000,
                'autoriza_monetizacion' => 1,
                'formato_audio' => 'WAV',
                'precio_licencia' => 39.99
            ]
        ]);
    }
}
