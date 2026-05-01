<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_actor',
        'tipo_accion',
        'entidad',
        'id_entidad',
        'datos_antes',
        'datos_despues',
        'ip_origen',
        'user_agent',
        'fecha'
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_actor', 'id');
    }
}
