<?php

namespace Database\Seeders;

// App\Models\User eliminado en Fase 2 — el modelo autenticable es App\Models\Usuario.
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Orden obligatorio:
     * 1. RolSeeder      → catálogo de roles (debe existir antes que usuario_rol)
     * 2. UsuarioSeeder  → usuarios + asignaciones en usuario_rol
     * 3. BeatSeeder     → contenido de demo
     * 4. ColeccionSeeder / ColeccionBeatSeeder → relaciones
     */
    public function run()
    {
        $this->call([
            RolSeeder::class,       // 1. Catálogo roles
            UsuarioSeeder::class,   // 2. Usuarios y pivote usuario_rol
            LicenciaSeeder::class,  // 3. Catálogo licencias
            ServicioSeeder::class,  // 4. Catálogo servicios
            BeatSeeder::class,      // 5. Beats y beat_licencia
            ColeccionSeeder::class, // 6. Colección y coleccion_beat
            CompraSeeder::class,    // 7. Compra, factura, pago, contrato
        ]);
    }
}
