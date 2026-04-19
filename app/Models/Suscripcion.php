<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function planPorRol()
    {
        return $this->belongsTo(PlanPorRol::class, 'id_plan_rol');
    }
}
