<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de suscripción activa, cancelada o expirada de un usuario.
 *
 * Conecta usuario, rol profesional y plan elegido, incluyendo fechas y
 * renovación automática.
 */
class Suscripcion extends Model
{
    protected $table = 'suscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_plan_rol',
        'id_rol',
        'estado_suscripcion',
        'fecha_inicio',
        'fecha_fin',
        'renovacion_auto',
        'tipo_pago'
    ];

    /**
     * Configuración de plan por rol asociada a la suscripción.
     */
    public function planPorRol()
    {
        return $this->belongsTo(PlanPorRol::class, 'id_plan_rol');
    }
}
