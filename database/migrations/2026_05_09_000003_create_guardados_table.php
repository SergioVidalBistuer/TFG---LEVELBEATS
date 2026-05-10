<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRACIÓN DE REFERENCIA — NO EJECUTAR AUTOMÁTICAMENTE.
 *
 * El SQL equivalente para phpMyAdmin está al final del archivo.
 * Aplica el SQL manualmente en phpMyAdmin antes de usar esta funcionalidad.
 *
 * SQL para phpMyAdmin:
 * -----------------------------------------------------------------------
 * CREATE TABLE `guardados` (
 *   `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 *   `id_usuario`     BIGINT UNSIGNED NOT NULL,
 *   `guardable_type` VARCHAR(20)     NOT NULL COMMENT 'beat | coleccion | servicio',
 *   `guardable_id`   BIGINT UNSIGNED NOT NULL,
 *   `fecha_guardado` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `guardados_unique` (`id_usuario`, `guardable_type`, `guardable_id`),
 *   KEY `guardados_usuario_idx` (`id_usuario`),
 *   KEY `guardados_tipo_idx` (`guardable_type`, `guardable_id`),
 *   CONSTRAINT `fk_guardados_usuario`
 *     FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 * -----------------------------------------------------------------------
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->string('guardable_type', 20);  // beat | coleccion | servicio
            $table->unsignedBigInteger('guardable_id');
            $table->timestamp('fecha_guardado')->useCurrent();

            // Clave única para evitar duplicados
            $table->unique(['id_usuario', 'guardable_type', 'guardable_id'], 'guardados_unique');

            // Índices de rendimiento
            $table->index('id_usuario', 'guardados_usuario_idx');
            $table->index(['guardable_type', 'guardable_id'], 'guardados_tipo_idx');

            // FK al usuario
            $table->foreign('id_usuario', 'fk_guardados_usuario')
                  ->references('id')->on('usuario')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardados');
    }
};
