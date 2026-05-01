<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plan';
    public $timestamps = false;

    protected $fillable = [
        'nombre_plan',
        'precio_mensual',
        'beneficios_generales'
    ];

    public function planesPorRol()
    {
        return $this->hasMany(PlanPorRol::class, 'id_plan');
    }
}
