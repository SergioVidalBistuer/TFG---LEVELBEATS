<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    // La tabla no usa created_at / updated_at de Laravel
    public $timestamps = false;

    protected $fillable = [
        'nombre_usuario',
        'direccion_correo',
        'contrasena',
        'google_id',
        // 'rol' eliminado: ya no existe como columna. Los roles son N:N via usuario_rol.
        'verificacion_completada',
        'url_foto_perfil',
        'descripcion_perfil',
        'perfil_publico',
        'fecha_registro',
        'calle',
        'localidad',
        'provincia',
        'pais',
        'codigo_postal',
    ];

    protected $hidden = [
        'contrasena', // nunca se serializa
    ];

    protected $casts = [
        'verificacion_completada' => 'boolean',
        'perfil_publico' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTH: mapeamos los campos no estándar al contrato Authenticatable
    |--------------------------------------------------------------------------
    | getAuthPassword()     → devuelve el hash almacenado en 'contrasena'
    | getAuthPasswordName() → le dice a EloquentUserProvider qué clave del
    |                         array de credenciales es la contraseña, de modo
    |                         que Auth::attempt(['contrasena' => ...]) funcione.
    |--------------------------------------------------------------------------
    */

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    /*
    |--------------------------------------------------------------------------
    | REMEMBER TOKEN deshabilitado
    |--------------------------------------------------------------------------
    | La tabla 'usuario' no tiene columna remember_token.
    | Sin esto, Auth::logout() intenta escribir en esa columna y falla.
    |--------------------------------------------------------------------------
    */

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // No soportado: la tabla no tiene columna remember_token
    }

    public function getRememberTokenName(): string
    {
        return ''; // cadena vacía → Laravel no intentará persistir el token
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // 1 Usuario tiene muchos Beats
    public function beats()
    {
        return $this->hasMany(Beat::class, 'id_usuario');
    }

    // 1 Usuario tiene muchas Colecciones
    public function colecciones()
    {
        return $this->hasMany(Coleccion::class, 'id_usuario');
    }

    // Usuario como comprador
    public function comprasComoComprador()
    {
        return $this->hasMany(Compra::class, 'id_usuario_comprador');
    }

    // Usuario como vendedor
    public function comprasComoVendedor()
    {
        return $this->hasMany(Compra::class, 'id_usuario_vendedor');
    }

    /**
     * Nuevas relaciones de Fase 4 (Esquema completo)
     */
    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'id_usuario');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_usuario');
    }

    // Productos guardados por el usuario
    public function guardados(): HasMany
    {
        return $this->hasMany(Guardado::class, 'id_usuario');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_usuario');
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'id_usuario_emisor');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'id_usuario_receptor');
    }

    public function conversacionesComoUsuarioUno(): HasMany
    {
        return $this->hasMany(Conversacion::class, 'usuario_uno_id');
    }

    public function conversacionesComoUsuarioDos(): HasMany
    {
        return $this->hasMany(Conversacion::class, 'usuario_dos_id');
    }

    public function mensajesDirectosEnviados(): HasMany
    {
        return $this->hasMany(MensajeDirecto::class, 'emisor_id');
    }

    public function analitica()
    {
        return $this->hasOne(Analitica::class, 'id_usuario');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario');
    }

    /*
    |--------------------------------------------------------------------------
    | FASE 2 – Sistema Multirol (preparado, pendiente de activar)
    |--------------------------------------------------------------------------
    | Descomentar cuando se cree el modelo Rol y la tabla usuario_rol.
    |--------------------------------------------------------------------------
    */

    /**
     * Roles N:N del usuario a través de la tabla pivote usuario_rol.
     * Un usuario puede tener múltiples roles simultáneamente.
     * Solo devuelve roles activos (rol_activo = 1) por defecto.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'usuario_rol',
            'id_usuario',   // FK de este modelo en el pivote
            'id_rol'        // FK del modelo relacionado en el pivote
        )->withPivot('rol_activo', 'fecha_alta_rol');
    }

    /**
     * Comprueba si el usuario tiene un rol concreto activado.
     * Hace una consulta directa a BD para no depender de eager-loading.
     *
     * Uso: $usuario->tieneRol('admin')  |  $usuario->tieneRol('productor')
     */
    public function tieneRol(string $nombre): bool
    {
        return $this->roles()
                    ->where('usuario_rol.rol_activo', 1)
                    ->where('nombre_rol', $nombre)
                    ->exists();
    }

    /**
     * Comprueba si el usuario es administrador de la plataforma.
     * Alias semántico de tieneRol('admin').
     */
    public function esAdmin(): bool
    {
        return $this->tieneRol('admin');
    }

    /**
     * Valida si el usuario tiene una Suscripción E-commerce activa 
     * en función del rol B2B pasado como parámetro.
     */
    public function tieneSuscripcionActiva(string $rolName): bool
    {
        if ($this->esAdmin()) {
            return true; // Bypass root
        }

        return $this->suscripciones()
                    ->where('estado_suscripcion', 'activa')
                    ->whereHas('planPorRol.rol', function ($q) use ($rolName) {
                        $q->where('nombre_rol', $rolName);
                    })
                    ->exists();
    }
}
