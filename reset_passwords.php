<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Reseteo directo por eloquent/query builder para máxima seguridad
DB::table('usuario')->where('direccion_correo', 'admin@levelbeats.com')->update(['contrasena' => Hash::make('admin123')]);
DB::table('usuario')->where('direccion_correo', 'lucia@levelbeats.com')->update(['contrasena' => Hash::make('123456')]);
DB::table('usuario')->where('direccion_correo', 'carlos@levelbeats.com')->update(['contrasena' => Hash::make('123456')]);

echo "Contraseñas reseteadas correctamente a nivel de BDD.\n";
