<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Coleccion
 *
 * Modelo que representa una colección de beats en la plataforma.
 */
class Coleccion extends Model
{
    /** @var string Nombre de la tabla asociada al modelo */
    protected $table = 'coleccion';

    /** @var array<int, string> Los atributos que se pueden asignar en masa */
    protected $fillable = [
        'id_usuario',
        'titulo_coleccion',
        'tipo_coleccion',
        'descripcion_coleccion',
        'estilo_genero',
        'precio',
        'es_destacada',
        'fecha_creacion'

    ];

    /** @var bool Indica si el modelo debe tener marcas de tiempo de creación y actualización */
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

    // Relación N:N con Compra
    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'coleccion_compra', 'id_coleccion', 'id_compra')
            ->withPivot('cantidad');
    }
}
