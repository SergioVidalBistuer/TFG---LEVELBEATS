<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coleccion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_usuario')
                ->constrained('usuario')
                ->cascadeOnDelete();

            $table->string('titulo_coleccion', 140);

            $table->enum('tipo_coleccion', ['publica','privada'])->default('privada');

            $table->text('descripcion_coleccion')->nullable();
            $table->string('estilo_genero', 80)->nullable();
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->boolean('es_destacada')->default(false);

            $table->dateTime('fecha_creacion')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleccion');
    }
};
