<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT

            $table->string('nombre_usuario', 80);
            $table->string('direccion_correo', 120)->unique();
            $table->string('contrasena', 255);

            $table->boolean('verificacion_completada')->default(false);

            $table->string('url_foto_perfil', 255)->nullable();
            $table->text('descripcion_perfil')->nullable();

            // DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            $table->dateTime('fecha_registro')->useCurrent();

            $table->string('calle', 120)->nullable();
            $table->string('localidad', 80)->nullable();
            $table->string('provincia', 80)->nullable();
            $table->string('pais', 80)->nullable();
            $table->string('codigo_postal', 20)->nullable();

            $table->index('nombre_usuario', 'idx_usuario_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
