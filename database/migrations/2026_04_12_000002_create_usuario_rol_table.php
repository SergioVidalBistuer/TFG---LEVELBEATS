<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Relación N:N entre usuario y rol.
 * Corresponde a la tabla 'usuario_rol' del nuevo esquema MySQL (levelbeat.sql).
 *
 * Un usuario puede tener varios roles simultáneamente (comprador + productor),
 * y cada rol puede tener muchos usuarios.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $table) {
            // PK compuesta (id_usuario, id_rol) — evita duplicados
            $table->foreignId('id_usuario')
                ->constrained('usuario')
                ->cascadeOnDelete();

            $table->foreignId('id_rol')
                ->constrained('rol')
                ->cascadeOnDelete();

            // Permite desactivar un rol sin borrar el registro histórico
            $table->boolean('rol_activo')->default(true);

            // Fecha de alta del rol — DEFAULT CURRENT_TIMESTAMP
            $table->dateTime('fecha_alta_rol')->useCurrent();

            $table->primary(['id_usuario', 'id_rol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};
