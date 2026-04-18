<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColeccionSeeder extends Seeder
{
    public function run(): void
    {
        $ids = DB::table('usuario')
            ->whereIn('direccion_correo', [
                'carlos@levelbeats.com',
                'miguel@levelbeats.com',
                'nova@levelbeats.com',
            ])
            ->pluck('id', 'direccion_correo');

        $carlos = $ids['carlos@levelbeats.com'] ?? null;
        $miguel = $ids['miguel@levelbeats.com'] ?? null;
        $nova   = $ids['nova@levelbeats.com'] ?? null;

        if (!$carlos || !$miguel || !$nova) {
            throw new \Exception("Faltan usuarios seed: carlos/miguel/nova. Ejecuta UsuarioSeeder antes.");
        }

        DB::table('coleccion')->insert([
            [
                'id_usuario' => $carlos,
                'titulo_coleccion' => 'Melodic Trap Essentials',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Selección de beats melódicos perfectos para voces y temas emocionales.',
                'estilo_genero' => 'Melodic Trap',
                'precio' => 24.99,
                'es_destacada' => 1,
                'fecha_creacion' => now()->subDays(10),
            ],
            [
                'id_usuario' => $miguel,
                'titulo_coleccion' => 'UK Drill Pack',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Colección agresiva para drill: kicks duros, 808s y hats rápidos.',
                'estilo_genero' => 'UK Drill',
                'precio' => 29.99,
                'es_destacada' => 1,
                'fecha_creacion' => now()->subDays(8),
            ],
            [
                'id_usuario' => $nova,
                'titulo_coleccion' => 'Club / Deep House',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Beats con vibra club y deep house. Perfectos para sesiones y edits.',
                'estilo_genero' => 'Deep House / Club Trap',
                'precio' => 19.99,
                'es_destacada' => 0,
                'fecha_creacion' => now()->subDays(6),
            ],
            [
                'id_usuario' => $carlos,
                'titulo_coleccion' => 'Trap Hits',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Trap generalista: bangers listos para grabar.',
                'estilo_genero' => 'Trap',
                'precio' => 22.99,
                'es_destacada' => 0,
                'fecha_creacion' => now()->subDays(5),
            ],
            [
                'id_usuario' => $nova,
                'titulo_coleccion' => 'Piano / Sad Pack',
                'tipo_coleccion' => 'publica',
                'descripcion_coleccion' => 'Beats con piano, vibra sad y emocional (piano trap / emo / lofi).',
                'estilo_genero' => 'Piano Trap',
                'precio' => 17.99,
                'es_destacada' => 0,
                'fecha_creacion' => now()->subDays(4),
            ],
        ]);
    }
}
