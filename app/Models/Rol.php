<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para la tabla 'rol'.
 * Catálogo de roles de la plataforma LevelBeat.
 *
 * Roles base: admin, usuario, productor, ingeniero
 */
class Rol extends Model
{
    protected $table = 'rol';

    // La tabla es un catálogo estático; no usa created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Un rol pertenece a muchos usuarios (N:N via usuario_rol).
     * FK: usuario_rol.id_rol -> rol.id
     */
    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuario_rol',
            'id_rol',       // FK del lado de Rol en el pivote
            'id_usuario'    // FK del lado de Usuario en el pivote
        )->withPivot('rol_activo', 'fecha_alta_rol');
    }
}
