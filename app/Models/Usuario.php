<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Usuario
 *
 * Modelo personalizado que representa a un usuario registrado en la plataforma.
 */
class Usuario extends Model
{
    /** @var string Nombre de la tabla en base de datos */
    protected $table = 'usuario';

    /** @var array<string, string> Casts de atributos */
    protected $casts = [
        'verificacion_completada' => 'boolean',
    ];
    /** @var array<int, string> Atributos asignables masivamente */
    protected $fillable = [
        'nombre_usuario',
        'direccion_correo',
        'contrasena',
        'rol',
        'verificacion_completada',
        'url_foto_perfil',
        'descripcion_perfil',
        'fecha_registro',
        'calle',
        'localidad',
        'provincia',
        'pais',
        'codigo_postal'
    ];

    /** @var bool Indica si la tabla usa timestamps integrados */
    public $timestamps = false; // tu tabla no usa created_at ni updated_at


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // 1 Usuario tiene muchos Beats
    public function beats()
    {
        return $this->hasMany(Beat::class, 'id_usuario');
    }

    // 1 Usuario tiene muchas Colecciones
    public function colecciones()
    {
        return $this->hasMany(Coleccion::class, 'id_usuario');
    }

    // Usuario como comprador
    public function comprasComoComprador()
    {
        return $this->hasMany(Compra::class, 'id_usuario_comprador');
    }

    // Usuario como vendedor
    public function comprasComoVendedor()
    {
        return $this->hasMany(Compra::class, 'id_usuario_vendedor');
    }
}
