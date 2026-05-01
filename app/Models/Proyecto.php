<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_servicio',
        'titulo_proyecto',
        'estado_proyecto',
        'notas_proyecto',
        'ruta_carpeta_archivos',
        'fecha_creacion',
    ];

    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'id_proyecto');
    }
}
