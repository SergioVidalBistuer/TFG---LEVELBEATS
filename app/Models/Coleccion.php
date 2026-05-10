<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coleccion extends Model
{
    protected $table = 'coleccion';

    protected $fillable = [
        'id_usuario',
        'titulo_coleccion',
        'tipo_coleccion',
        'descripcion_coleccion',
        'estilo_genero',
        'precio',
        'es_destacada',
        'activo_publicado',
        'fecha_creacion'
    ];

    public $timestamps = false; // tu tabla no usa created_at ni updated_at

    protected $casts = [
        'es_destacada' => 'boolean',
        'activo_publicado' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Muchas colecciones pertenecen a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // Relación N:N con Beat
    public function beats()
    {
        return $this->belongsToMany(
            Beat::class,
            'coleccion_beat',
            'id_coleccion',
            'id_beat'
        );
    }

    public function compras()
    {
        return $this->belongsToMany(
            Compra::class,
            'coleccion_compra',
            'id_coleccion',
            'id_compra'
        );
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('activo_publicado', true);
    }
}
