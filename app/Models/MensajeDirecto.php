<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de mensaje directo entre usuarios.
 *
 * Pertenece a una conversación y permite marcar lectura de mensajes recibidos.
 */
class MensajeDirecto extends Model
{
    protected $table = 'mensaje_directo';
    public $timestamps = false;

    protected $fillable = [
        'conversacion_id',
        'emisor_id',
        'cuerpo',
        'leido',
        'fecha_envio',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha_envio' => 'datetime',
    ];

    /**
     * Conversación a la que pertenece el mensaje.
     */
    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class, 'conversacion_id');
    }

    /**
     * Usuario emisor del mensaje.
     */
    public function emisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'emisor_id');
    }
}
