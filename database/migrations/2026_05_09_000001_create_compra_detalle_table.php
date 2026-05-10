<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compra_detalle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_compra')
                ->constrained('compra')
                ->cascadeOnDelete();

            $table->enum('tipo_producto', ['beat', 'coleccion']);
            $table->unsignedBigInteger('id_producto');

            $table->foreignId('id_licencia')
                ->nullable()
                ->constrained('licencia')
                ->nullOnDelete();

            $table->decimal('precio_base_producto', 10, 2)->default(0);
            $table->decimal('precio_licencia', 10, 2)->default(0);
            $table->decimal('precio_final', 10, 2)->default(0);

            $table->string('nombre_producto_snapshot', 160);
            $table->string('nombre_licencia_snapshot', 120)->nullable();
            $table->string('formato_incluido_snapshot', 120)->nullable();
            $table->text('derechos_snapshot')->nullable();

            $table->dateTime('fecha')->useCurrent();

            $table->index(['tipo_producto', 'id_producto']);
            $table->index(['id_compra', 'tipo_producto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_detalle');
    }
};
