<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalle';

    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'tipo_producto',
        'id_producto',
        'id_licencia',
        'precio_base_producto',
        'precio_licencia',
        'precio_final',
        'nombre_producto_snapshot',
        'nombre_licencia_snapshot',
        'formato_incluido_snapshot',
        'derechos_snapshot',
        'fecha',
    ];

    protected $casts = [
        'precio_base_producto' => 'decimal:2',
        'precio_licencia' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id');
    }

    public function licencia(): BelongsTo
    {
        return $this->belongsTo(Licencia::class, 'id_licencia', 'id');
    }
}
