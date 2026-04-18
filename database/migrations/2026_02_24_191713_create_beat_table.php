<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('beat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_usuario')
                ->constrained('usuario')
                ->cascadeOnDelete();

            $table->string('titulo_beat', 140);
            $table->string('genero_musical', 80)->nullable();
            $table->integer('tempo_bpm')->nullable();

            $table->enum('tono_musical', ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'])->nullable();

            $table->string('estado_de_animo', 80)->nullable();

            $table->decimal('precio_base_licencia', 10, 2)->default(0);

            $table->string('url_audio_previsualizacion', 255)->nullable();
            $table->string('url_archivo_final', 255)->nullable();
            $table->string('url_portada_beat', 255)->nullable();

            $table->boolean('activo_publicado')->default(false);
            $table->dateTime('fecha_publicacion')->nullable();

            $table->index('id_usuario', 'idx_beat_usuario');
            $table->index('titulo_beat', 'idx_beat_titulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beat');
    }
};
