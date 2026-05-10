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
        'ingeniero_aceptado_at',
        'cliente_aceptado_at',
        'cancelado_at',
        'cancelado_por',
        'id_compra',
        'fecha_creacion',
    ];

    protected $casts = [
        'ingeniero_aceptado_at' => 'datetime',
        'cliente_aceptado_at' => 'datetime',
        'cancelado_at' => 'datetime',
        'fecha_creacion' => 'datetime',
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

    public function archivos()
    {
        return $this->hasMany(ArchivoProyecto::class, 'id_proyecto');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function usuarioCancelador()
    {
        return $this->belongsTo(Usuario::class, 'cancelado_por');
    }
}
