<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    protected $table = 'licencia';
    public $timestamps = false; // Manejado por columna explicita si la hubiera

    protected $fillable = [
        'tipo_licencia',
        'nombre_licencia',
        'descripcion_licencia',
        'duracion_meses',
        'reproducciones_max',
        'autoriza_monetizacion',
        'formato_audio',
        'precio_licencia',
    ];

    protected $casts = [
        'autoriza_monetizacion' => 'boolean',
        'precio_licencia' => 'decimal:2',
        'duracion_meses' => 'integer',
        'reproducciones_max' => 'integer',
    ];
}
