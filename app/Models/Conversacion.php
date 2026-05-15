<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modelo de conversación directa entre dos usuarios.
 *
 * Guarda los participantes, fecha del último mensaje y relaciones necesarias
 * para listar, mostrar y validar acceso a la mensajería directa.
 */
class Conversacion extends Model
{
    protected $table = 'conversacion';
    public $timestamps = false;

    protected $fillable = [
        'usuario_uno_id',
        'usuario_dos_id',
        'ultimo_mensaje_at',
        'fecha_creacion',
    ];

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime',
        'fecha_creacion' => 'datetime',
    ];

    /**
     * Primer participante normalizado de la conversación.
     */
    public function usuarioUno(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_uno_id');
    }

    /**
     * Segundo participante normalizado de la conversación.
     */
    public function usuarioDos(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_dos_id');
    }

    /**
     * Mensajes ordenados cronológicamente.
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeDirecto::class, 'conversacion_id')->orderBy('fecha_envio');
    }

    /**
     * Último mensaje para previsualización en listados.
     */
    public function ultimoMensaje(): HasOne
    {
        return $this->hasOne(MensajeDirecto::class, 'conversacion_id')->latestOfMany('fecha_envio');
    }

    /**
     * Scope para filtrar conversaciones donde participa un usuario.
     */
    public function scopeParticipa(Builder $query, int $usuarioId): Builder
    {
        return $query->where(function ($q) use ($usuarioId) {
            $q->where('usuario_uno_id', $usuarioId)
              ->orWhere('usuario_dos_id', $usuarioId);
        });
    }

    /**
     * Comprueba en memoria si un usuario participa en esta conversación.
     */
    public function participa(int $usuarioId): bool
    {
        return (int) $this->usuario_uno_id === $usuarioId || (int) $this->usuario_dos_id === $usuarioId;
    }

    /**
     * Devuelve el otro participante respecto al usuario indicado.
     */
    public function otroUsuario(int $usuarioId): ?Usuario
    {
        if ((int) $this->usuario_uno_id === $usuarioId) {
            return $this->usuarioDos;
        }

        if ((int) $this->usuario_dos_id === $usuarioId) {
            return $this->usuarioUno;
        }

        return null;
    }
}
