<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensaje';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_emisor',
        'id_usuario_receptor',
        'id_proyecto',
        'contenido_mensaje',
        'url_archivo_adjunto',
        'fecha_envio',
        'mensaje_leido',
    ];

    public function emisor()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_emisor');
    }

    public function receptor()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_receptor');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}
