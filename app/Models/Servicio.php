<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicio';
    public $timestamps = false; // Manejado por columna explicita si la hubiera

    protected $fillable = [
        'id_usuario',
        'descripcion_servicio',
        'numero_revisiones',
        'servicio_activo',
        'url_portafolio',
        'tipo_servicio',
        'titulo_servicio',
        'precio_servicio',
        'plazo_entrega_dias',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_servicio');
    }
}
