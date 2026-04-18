<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Compra
 *
 * Modelo que representa una transacción o compra de beats y colecciones.
 */
class Compra extends Model
{
    
    /** @var string Nombre de la tabla asociada al modelo */
    protected $table = 'compra';

    
    /** @var string Clave primaria de la tabla */
    protected $primaryKey = 'id';

   
    /** @var bool Indica si el modelo incluye timestamps por defecto */
    public $timestamps = false;

   
    /** @var array<int, string> Los atributos asignables en masa */
    protected $fillable = [
        'id_usuario_comprador',
        'id_usuario_vendedor',
        'importe_total',
        'metodo_de_pago',
        'estado_compra',
        'url_contrato_pdf',
        'fecha_compra',
    ];

    /** @var array<string, string> Casts MySQL */
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
     * FK: factura.id_compra -> compra.id (ON DELETE CASCADE) :contentReference[oaicite:3]{index=3}
     */
    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'id_compra', 'id');
    }

    /**
     * Compra contiene muchos Beats (N:N) vía tabla pivote beat_compra
     * beat_compra(id_beat, id_compra, cantidad) PK compuesta :contentReference[oaicite:4]{index=4}
     */
    public function beats()
    {
        return $this->belongsToMany(Beat::class, 'beat_compra', 'id_compra', 'id_beat')
            ->withPivot('cantidad');
    }



    /**
     * Compra contiene muchas Colecciones (N:N) vía tabla pivote coleccion_compra
     * coleccion_compra(id_coleccion, id_compra, cantidad) PK compuesta :contentReference[oaicite:5]{index=5}
     */
    public function colecciones()
    {
        return $this->belongsToMany(Coleccion::class, 'coleccion_compra', 'id_compra', 'id_coleccion')
            ->withPivot('cantidad');
    }
}
