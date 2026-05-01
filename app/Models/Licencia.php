<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    protected $table = 'licencia';
    public $timestamps = false; // Manejado por columna explicita si la hubiera

    protected $fillable = []; // A completar en el futuro
}
