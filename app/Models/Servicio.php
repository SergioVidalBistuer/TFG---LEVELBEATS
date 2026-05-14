<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Servicio extends Model
{
    private static ?string $portadaColumnCache = null;

    protected $table = 'servicio';
    public $timestamps = false; // Manejado por columna explicita si la hubiera

    protected $fillable = [
        'id_usuario',
        'descripcion_servicio',
        'numero_revisiones',
        'servicio_activo',
        'url_portafolio',
        'url_portada_servicio',
        'imagen_portada',
        'portada',
        'imagen',
        'url_imagen',
        'imagen_servicio',
        'caratula',
        'cover',
        'tipo_servicio',
        'titulo_servicio',
        'precio_servicio',
        'plazo_entrega_dias',
    ];

    public const PORTADA_COLUMNS = [
        'url_portada_servicio',
        'imagen_portada',
        'portada',
        'imagen',
        'url_imagen',
        'imagen_servicio',
        'caratula',
        'cover',
    ];

    public static function portadaColumn(): ?string
    {
        if (self::$portadaColumnCache !== null) {
            return self::$portadaColumnCache;
        }

        foreach (self::PORTADA_COLUMNS as $column) {
            if (Schema::hasColumn('servicio', $column)) {
                return self::$portadaColumnCache = $column;
            }
        }

        return self::$portadaColumnCache = '';
    }

    public function getPortadaUrlAttribute(): ?string
    {
        foreach (self::PORTADA_COLUMNS as $column) {
            if (!empty($this->attributes[$column])) {
                return $this->attributes[$column];
            }
        }

        return null;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_servicio');
    }

    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'servicio_compra', 'id_servicio', 'id_compra');
    }
}
