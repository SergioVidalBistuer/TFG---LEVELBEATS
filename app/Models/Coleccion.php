<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Modelo de colección de beats.
 *
 * Agrupa beats de un productor y mantiene compatibilidad con distintos nombres
 * históricos de columna para portada.
 */
class Coleccion extends Model
{
    private static ?string $portadaColumnCache = null;

    protected $table = 'coleccion';

    protected $fillable = [
        'id_usuario',
        'titulo_coleccion',
        'tipo_coleccion',
        'descripcion_coleccion',
        'estilo_genero',
        'url_portada_coleccion',
        'imagen_portada',
        'portada',
        'imagen',
        'url_imagen',
        'imagen_coleccion',
        'caratula',
        'cover',
        'precio',
        'es_destacada',
        'activo_publicado',
        'fecha_creacion'
    ];

    public const PORTADA_COLUMNS = [
        'url_portada_coleccion',
        'imagen_portada',
        'portada',
        'imagen',
        'url_imagen',
        'imagen_coleccion',
        'caratula',
        'cover',
    ];

    /**
     * Detecta qué columna de portada existe en la base de datos actual.
     */
    public static function portadaColumn(): ?string
    {
        if (self::$portadaColumnCache !== null) {
            return self::$portadaColumnCache;
        }

        foreach (self::PORTADA_COLUMNS as $column) {
            if (Schema::hasColumn('coleccion', $column)) {
                return self::$portadaColumnCache = $column;
            }
        }

        return self::$portadaColumnCache = '';
    }

    /**
     * Devuelve la primera portada disponible entre las columnas soportadas.
     */
    public function getPortadaUrlAttribute(): ?string
    {
        foreach (self::PORTADA_COLUMNS as $column) {
            if (!empty($this->attributes[$column])) {
                return $this->attributes[$column];
            }
        }

        return null;
    }

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

    /**
     * Usuario productor propietario de la colección.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Beats incluidos en la colección.
     */
    public function beats()
    {
        return $this->belongsToMany(
            Beat::class,
            'coleccion_beat',
            'id_coleccion',
            'id_beat'
        );
    }

    /**
     * Compras históricas asociadas mediante tabla pivote legacy.
     */
    public function compras()
    {
        return $this->belongsToMany(
            Compra::class,
            'coleccion_compra',
            'id_coleccion',
            'id_compra'
        );
    }

    /**
     * Limita una consulta a colecciones visibles públicamente.
     */
    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('activo_publicado', true);
    }
}
