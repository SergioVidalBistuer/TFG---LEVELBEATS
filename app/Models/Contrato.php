<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model
{
    protected $table = 'contrato';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'tipo_contrato',
        'url_contrato_pdf',
        'contrato_firmado',
        'fecha_firma',
    ];

    protected $casts = [
        'contrato_firmado' => 'boolean',
        'fecha_firma' => 'datetime',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
}
