<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Beat extends Model
{
    /** Tu tabla en BD es singular */
    protected $table = 'beat';

    /** PK */
    protected $primaryKey = 'id';

    /** No tienes created_at / updated_at en beat */
    public $timestamps = false;

    /** Campos permitidos en create()/update() */
    protected $fillable = [
        'id_usuario',
        'titulo_beat',
        'genero_musical',
        'tempo_bpm',
        'tono_musical',
        'estado_de_animo',
        'precio_base_licencia',
        'url_audio_previsualizacion',
        'url_archivo_final',
        'url_portada_beat',
        'activo_publicado',
        'fecha_publicacion',
    ];

    /** Casts para trabajar cómodo */
    protected $casts = [
        'activo_publicado' => 'boolean',
        'precio_base_licencia' => 'decimal:2',
        'fecha_publicacion' => 'datetime',
        'tempo_bpm' => 'integer',
    ];

    /* =========================
     * RELACIONES ELOQUENT
     * ========================= */

    /** Un Beat pertenece a un Usuario (beat.id_usuario -> usuario.id) */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    /**
     * Un Beat pertenece a muchas colecciones (pivot: coleccion_beat)
     * (coleccion_beat.id_beat -> beat.id) y (coleccion_beat.id_coleccion -> coleccion.id)
     */
    public function colecciones(): BelongsToMany
    {
        return $this->belongsToMany(
            Coleccion::class,
            'coleccion_beat',
            'id_beat',
            'id_coleccion'
        );
    }

    /**
     * Un Beat pertenece a muchas compras (pivot: beat_compra)
     * (beat_compra.id_beat -> beat.id) y (beat_compra.id_compra -> compra.id)
     */
    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'beat_compra', 'id_beat', 'id_compra');
    }

    /**
     * Un Beat tiene muchas licencias disponibles para la venta.
     */
    public function licencias()
    {
        return $this->hasMany(Licencia::class, 'id_beat');
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('activo_publicado', true);
    }
}
