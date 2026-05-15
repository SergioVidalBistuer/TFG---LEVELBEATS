<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modelo de compra realizada en LevelBeats.
 *
 * Reúne comprador, vendedor institucional/productor, líneas de detalle,
 * productos adquiridos, servicios contratados, factura y contrato.
 */
class Compra extends Model
{
    /** Tu tabla en BD es singular */
    protected $table = 'compra';

    /** PK */
    protected $primaryKey = 'id';

    /** No hay created_at / updated_at (tienes fecha_compra propia) */
    public $timestamps = false;

    /** Campos asignables masivamente */
    protected $fillable = [
        'id_usuario_comprador',
        'id_usuario_vendedor',
        'importe_total',
        'metodo_de_pago',
        'estado_compra',
        'url_contrato_pdf',
        'fecha_compra',
    ];

    /** Casts recomendados según tipos MySQL */
    protected $casts = [
        'importe_total' => 'decimal:2',
        'fecha_compra' => 'datetime',
    ];

    /* =========================
     * RELACIONES ELOQUENT
     * ========================= */

    /**
     * Compra pertenece a un Usuario (comprador)
     * FK: compra.id_usuario_comprador -> usuario.id (ON DELETE RESTRICT) :contentReference[oaicite:1]{index=1}
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_comprador', 'id');
    }

    /**
     * Compra pertenece a un Usuario (vendedor)
     * FK: compra.id_usuario_vendedor -> usuario.id (ON DELETE RESTRICT) :contentReference[oaicite:2]{index=2}
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_vendedor', 'id');
    }

    /**
     * Compra tiene una Factura (1:1 real porque factura.id_compra es UNIQUE)
     * FK: factura.id_compra -> compra.id (ON DELETE CASCADE)
     */
    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'id_compra', 'id');
    }

    /**
     * Compra tiene un Contrato (1:1 real porque contrato.id_compra es UNIQUE)
     * FK: contrato.id_compra -> compra.id (ON DELETE CASCADE)
     */
    public function contrato(): HasOne
    {
        return $this->hasOne(Contrato::class, 'id_compra', 'id');
    }

    /**
     * Compra contiene muchos Beats (N:N) vía tabla pivote beat_compra
     * beat_compra(id_beat, id_compra) PK compuesta (sin cantidad en el nuevo esquema)
     */
    public function beats()
    {
        return $this->belongsToMany(Beat::class, 'beat_compra', 'id_compra', 'id_beat');
    }

    /**
     * Compra contiene muchas Colecciones vía tabla pivote coleccion_compra
     */
    public function colecciones()
    {
        return $this->belongsToMany(Coleccion::class, 'coleccion_compra', 'id_compra', 'id_coleccion');
    }

    /**
     * Detalles normalizados de la compra con snapshots de producto/licencia.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class, 'id_compra', 'id');
    }

    /**
     * Compra contiene muchos Servicios (N:N) vía tabla pivote servicio_compra
     */
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_compra', 'id_compra', 'id_servicio');
    }
}
