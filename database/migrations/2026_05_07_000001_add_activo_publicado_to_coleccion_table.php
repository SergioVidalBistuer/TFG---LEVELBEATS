<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coleccion', function (Blueprint $table) {
            $table->boolean('activo_publicado')->default(true)->after('es_destacada');
        });
    }

    public function down(): void
    {
        Schema::table('coleccion', function (Blueprint $table) {
            $table->dropColumn('activo_publicado');
        });
    }
};
