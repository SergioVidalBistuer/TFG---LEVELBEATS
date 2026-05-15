<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de plan comercial.
 *
 * Define nombre, precio mensual y beneficios generales reutilizados por los
 * planes específicos de productor o ingeniero.
 */
class Plan extends Model
{
    protected $table = 'plan';
    public $timestamps = false;

    protected $fillable = [
        'nombre_plan',
        'precio_mensual',
        'beneficios_generales'
    ];

    /**
     * Asociaciones del plan con roles concretos y límites funcionales.
     */
    public function planesPorRol()
    {
        return $this->hasMany(PlanPorRol::class, 'id_plan');
    }
}
