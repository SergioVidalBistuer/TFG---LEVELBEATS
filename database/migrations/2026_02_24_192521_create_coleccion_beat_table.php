<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coleccion_beat', function (Blueprint $table) {
            $table->foreignId('id_coleccion')
                ->constrained('coleccion')
                ->cascadeOnDelete();

            $table->foreignId('id_beat')
                ->constrained('beat')
                ->cascadeOnDelete();

            $table->primary(['id_coleccion', 'id_beat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleccion_beat');
    }
};
