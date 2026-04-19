<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pago';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'importe_pago',
        'metodo_de_pago',
        'estado_pago',
        'fecha_pago',
    ];

    protected $casts = [
        'importe_pago' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
