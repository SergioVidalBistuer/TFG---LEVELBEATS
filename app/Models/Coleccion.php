<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coleccion extends Model
{
    protected $table = 'coleccion';

    protected $fillable = [
        'id_usuario',
        'titulo_coleccion',
        'tipo_coleccion',
        'descripcion_coleccion',
        'estilo_genero',
        'es_destacada',
        'fecha_creacion'
    ];

    public $timestamps = false; // tu tabla no usa created_at ni updated_at


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
}
