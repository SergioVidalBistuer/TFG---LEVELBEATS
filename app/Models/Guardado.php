<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Guardado extends Model
{
    protected $table = 'guardados';

    /*
     * La tabla no usa created_at / updated_at de Laravel estándar.
     * Usamos fecha_guardado para el timestamp de creación.
     */
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'guardable_type',
        'guardable_id',
        'fecha_guardado',
    ];

    protected $casts = [
        'fecha_guardado' => 'datetime',
        'guardable_id'   => 'integer',
        'id_usuario'     => 'integer',
    ];

    /* =========================
     * RELACIONES
     * ========================= */

    /**
     * El usuario propietario del guardado.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    /**
     * Relación polimórfica: apunta a Beat, Coleccion o Servicio.
     *
     * morphTo(name, type, id):
     *   - name: nombre de la relación (se usa para el morphMap lookup)
     *   - type: columna que contiene el tipo ('beat'|'coleccion'|'servicio')
     *   - id:   columna que contiene el id del producto relacionado
     *
     * El morphMap registrado en AppServiceProvider traduce los strings cortos
     * a clases Eloquent concretas sin necesidad de guardar FQCN en BD.
     */
    public function guardable(): MorphTo
    {
        return $this->morphTo('guardable', 'guardable_type', 'guardable_id');
    }

    /* =========================
     * HELPERS
     * ========================= */

    /**
     * Tipos de guardable permitidos con su clase Eloquent.
     */
    public static function tiposPermitidos(): array
    {
        return [
            'beat'      => Beat::class,
            'coleccion' => Coleccion::class,
            'servicio'  => Servicio::class,
        ];
    }

    /**
     * Resuelve el modelo a partir del tipo corto (beat, coleccion, servicio).
     * Devuelve null si el tipo no es válido.
     */
    public static function clasePorTipo(string $tipo): ?string
    {
        return static::tiposPermitidos()[$tipo] ?? null;
    }
}
