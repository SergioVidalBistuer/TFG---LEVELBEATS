<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('beat_compra', function (Blueprint $table) {
            $table->foreignId('id_beat')
                ->constrained('beat')
                ->cascadeOnDelete();

            $table->foreignId('id_compra')
                ->constrained('compra')
                ->cascadeOnDelete();

            $table->primary(['id_beat', 'id_compra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beat_compra');
    }
};
