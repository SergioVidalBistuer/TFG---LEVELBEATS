<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompraSeeder extends Seeder
{
    public function run()
    {
        $comprador = DB::table('usuario')->where('direccion_correo', 'lucia@levelbeats.com')->value('id');
        $vendedor = DB::table('usuario')->where('direccion_correo', 'carlos@levelbeats.com')->value('id');
        $beat = DB::table('beat')->where('id_usuario', $vendedor)->first();

        if (!$comprador || !$vendedor || !$beat) return;

        // 1. Compra
        $idCompra = DB::table('compra')->insertGetId([
            'id_usuario_comprador' => $comprador,
            'id_usuario_vendedor' => $vendedor,
            'importe_total' => $beat->precio_base_licencia,
            'metodo_de_pago' => 'tarjeta',
            'estado_compra' => 'pagada',
            'fecha_compra' => now(),
        ]);

        // 2. beat_compra
        DB::table('beat_compra')->insert([
            'id_beat' => $beat->id,
            'id_compra' => $idCompra
        ]);

        // 3. factura
        DB::table('factura')->insert([
            'id_compra' => $idCompra,
            'numero_factura' => 'INV-' . date('Y') . '-0001',
            'base_imponible' => $beat->precio_base_licencia * 0.8,
            'importe_impuestos' => $beat->precio_base_licencia * 0.2,
            'importe_total' => $beat->precio_base_licencia,
            'pago_confirmado' => 1,
            'fecha_emision' => now(),
        ]);

        // 4. contrato
        DB::table('contrato')->insert([
            'id_compra' => $idCompra,
            'tipo_contrato' => 'Lease Básico',
            'contrato_firmado' => 1,
            'fecha_firma' => now(),
        ]);

        // 5. pago
        DB::table('pago')->insert([
            'id_usuario' => $vendedor,
            'importe_pago' => $beat->precio_base_licencia * 0.9, 
            'metodo_de_pago' => 'transferencia',
            'estado_pago' => 'procesado',
            'fecha_pago' => now(),
        ]);
    }
}
