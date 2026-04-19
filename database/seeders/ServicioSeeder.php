<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    public function run()
    {
        $idIngeniero = DB::table('usuario')->where('direccion_correo', 'nova@levelbeats.com')->value('id');
        if (!$idIngeniero) return;

        DB::table('servicio')->insert([
            'id_usuario' => $idIngeniero,
            'descripcion_servicio' => 'Mezcla profesional de voz e instrumental.',
            'numero_revisiones' => 3,
            'servicio_activo' => 1,
            'tipo_servicio' => 'mezcla',
            'titulo_servicio' => 'Mezcla Vocal + Master',
            'precio_servicio' => 150.00,
            'plazo_entrega_dias' => 5
        ]);
    }
}
