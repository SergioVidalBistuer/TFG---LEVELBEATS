<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de factura emitida por una compra.
 *
 * Almacena importes, estado de pago, fecha de emisión y ruta del PDF generado.
 */
class Factura extends Model
{
    /** Tu tabla en BD es singular */
    protected $table = 'factura';

    /** PK */
    protected $primaryKey = 'id';

    /** No hay created_at / updated_at */
    public $timestamps = false;

    /** Asignación masiva */
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

    /** Casts coherentes con la BD */
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
