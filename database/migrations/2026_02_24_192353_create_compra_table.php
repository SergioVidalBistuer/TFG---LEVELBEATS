<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compra', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_usuario_comprador')
                ->constrained('usuario')
                ->restrictOnDelete();

            $table->foreignId('id_usuario_vendedor')
                ->constrained('usuario')
                ->restrictOnDelete();

            $table->decimal('importe_total', 10, 2);

            $table->enum('metodo_de_pago', ['tarjeta','paypal','stripe','transferencia']);
            $table->enum('estado_compra', ['pendiente','pagada','fallida','reembolsada']);

            $table->string('url_contrato_pdf', 255)->nullable();

            $table->dateTime('fecha_compra')->useCurrent();

            $table->index(['id_usuario_vendedor', 'fecha_compra'], 'idx_compra_vendedor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};
