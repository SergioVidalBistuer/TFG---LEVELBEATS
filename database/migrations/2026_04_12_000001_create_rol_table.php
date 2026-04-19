<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de roles de la plataforma LevelBeat.
 * Corresponde a la tabla 'rol' del nuevo esquema MySQL (levelbeat.sql).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_rol', 50)->unique();
            // Sin timestamps: la tabla es un catálogo estático.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
