<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Factura
 *
 * Modelo que representa una factura generada asociada a una compra.
 */
class Factura extends Model
{
    
    /** @var string Nombre de la tabla en base de datos */
    protected $table = 'factura';

    /** @var string PK */
    protected $primaryKey = 'id';

  
    /** @var bool Indica si usa timestamps por defecto */
    public $timestamps = false;

    /** @var array<int, string> Atributos asignables masivamente */
    protected $fillable = [
        'id_compra',
        'numero_factura',
        'base_imponible',
        'importe_impuestos',
        'importe_total',
        'url_factura_pdf',
        'pago_confirmado',
        'fecha_emision',
    ];

    /** @var array<string, string> Casts coherentes con la BD */
    protected $casts = [
        'base_imponible'     => 'decimal:2',
        'importe_impuestos'  => 'decimal:2',
        'importe_total'      => 'decimal:2',
        'pago_confirmado'    => 'boolean',
        'fecha_emision'      => 'datetime',
    ];

    /* =========================
     * RELACIONES ELOQUENT
     * ========================= */

    /**
     * Factura pertenece a una Compra (1:1 real por UNIQUE en factura.id_compra).
     * FK: factura.id_compra -> compra.id (ON DELETE CASCADE). :contentReference[oaicite:1]{index=1}
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id');
    }
}
