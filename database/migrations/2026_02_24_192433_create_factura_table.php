<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_compra')
                ->unique()
                ->constrained('compra')
                ->cascadeOnDelete();

            $table->string('numero_factura', 60)->unique();

            $table->decimal('base_imponible', 10, 2);
            $table->decimal('importe_impuestos', 10, 2);
            $table->decimal('importe_total', 10, 2);

            $table->string('url_factura_pdf', 255)->nullable();

            $table->boolean('pago_confirmado')->default(false);

            $table->dateTime('fecha_emision')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};
