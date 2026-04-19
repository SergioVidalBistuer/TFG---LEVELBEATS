<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPorRol extends Model
{
    protected $table = 'plan_por_rol';
    public $timestamps = false;

    protected $fillable = [
        'id_plan',
        'id_rol',
        'encargos_personalizados',
        'beats_publicables_mes',
        'cupones_activos_maximos',
        'qa_asistido_por_ia',
        'prioridad_soporte',
        'almacenamiento_gigabytes',
        'bundles_activos_maximos',
        'nivel_analiticas',
        'encargos_max_ingeniero',
        'revisiones_incluidas',
        'accesos_plantillas_premium',
        'modo_autotagging_ia',
        'colecciones_max_productor',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
}
