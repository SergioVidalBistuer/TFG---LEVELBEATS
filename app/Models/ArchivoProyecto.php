<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoProyecto extends Model
{
    protected $table = 'archivos_proyecto';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_usuario',
        'archivo',
        'fecha_subida',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
