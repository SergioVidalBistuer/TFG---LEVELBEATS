<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de proyecto o encargo asociado a un servicio.
 *
 * Representa el flujo de trabajo entre cliente e ingeniero, incluyendo estado,
 * mensajes, archivos, compra asociada y aceptación/cancelación.
 */
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

    /**
     * Cliente que creó el proyecto.
     */
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Servicio contratado que origina el proyecto.
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    /**
     * Mensajes internos del proyecto.
     */
    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'id_proyecto');
    }

    /**
     * Archivos subidos dentro del proyecto.
     */
    public function archivos()
    {
        return $this->hasMany(ArchivoProyecto::class, 'id_proyecto');
    }

    /**
     * Compra asociada cuando el encargo ha pasado por checkout.
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    /**
     * Usuario que canceló el proyecto, si existe.
     */
    public function usuarioCancelador()
    {
        return $this->belongsTo(Usuario::class, 'cancelado_por');
    }
}
