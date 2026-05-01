@extends('layouts.master')

@section('title', 'Editar Usuario')

@section('content')
    <div class="form-lb">
        <h1>Editar Usuario</h1>

        @if ($errors->any())
            <div class="form-lb__error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('usuario.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $usuario->id }}">

            <div class="form-lb__group">
                <label for="nombre_usuario">Nombre <span style="color: #ff6b6b;">*</span></label>
                <input id="nombre_usuario" class="form-lb__input" type="text" name="nombre_usuario"
                       value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" required placeholder="Nombre de usuario">
            </div>

            <div class="form-lb__group">
                <label for="direccion_correo">Email <span style="color: #ff6b6b;">*</span></label>
                <input id="direccion_correo" class="form-lb__input" type="email" name="direccion_correo"
                       value="{{ old('direccion_correo', $usuario->direccion_correo) }}" required placeholder="tu@email.com">
            </div>

            @if(auth()->check() && auth()->user()->esAdmin())
                <div class="form-lb__group">
                    <label for="rol">Rol</label>
                    <select id="rol" class="form-lb__select" name="rol">
                        @php
                            // Detectar el rol base actual del usuario editado desde la relación cargada
                            $rolBase = $usuario->roles->whereIn('nombre_rol', ['admin','usuario'])->first()?->nombre_rol ?? 'usuario';
                        @endphp
                        <option value="usuario" {{ old('rol', $rolBase) === 'usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin"   {{ old('rol', $rolBase) === 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            @endif

            {{-- SEPARADOR --}}
            <hr style="border: none; border-top: 1px solid var(--line); margin: 24px 0;">
            <p style="color: rgba(255,255,255,.5); font-size: 13px; text-align: center; margin-bottom: 18px;">Información adicional</p>

            <div class="form-lb__group">
                <label for="descripcion_perfil">Descripción de perfil</label>
                <textarea id="descripcion_perfil" class="form-lb__textarea" name="descripcion_perfil"
                          placeholder="Descripción del perfil...">{{ old('descripcion_perfil', $usuario->descripcion_perfil) }}</textarea>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="calle">Calle</label>
                    <input id="calle" class="form-lb__input" type="text" name="calle"
                           value="{{ old('calle', $usuario->calle) }}" placeholder="Dirección">
                </div>

                <div class="form-lb__group">
                    <label for="localidad">Localidad</label>
                    <input id="localidad" class="form-lb__input" type="text" name="localidad"
                           value="{{ old('localidad', $usuario->localidad) }}" placeholder="Ciudad">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="provincia">Provincia</label>
                    <input id="provincia" class="form-lb__input" type="text" name="provincia"
                           value="{{ old('provincia', $usuario->provincia) }}" placeholder="Provincia">
                </div>

                <div class="form-lb__group">
                    <label for="pais">País</label>
                    <input id="pais" class="form-lb__input" type="text" name="pais"
                           value="{{ old('pais', $usuario->pais) }}" placeholder="España">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="codigo_postal">Código Postal</label>
                    <input id="codigo_postal" class="form-lb__input" type="text" name="codigo_postal"
                           value="{{ old('codigo_postal', $usuario->codigo_postal) }}" placeholder="28001">
                </div>
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary" type="submit">Actualizar</button>
                <a class="btn btn--ghost" href="{{ route('usuario.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
