@extends('layouts.master')

@section('title', 'Crear Usuario')

@section('content')
    <div class="form-lb">
        <h1>Crear Usuario</h1>

        @if ($errors->any())
            <div class="form-lb__error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('usuario.save') }}">
            @csrf

            <div class="form-lb__group">
                <label for="nombre_usuario">Nombre <span style="color: #ff6b6b;">*</span></label>
                <input id="nombre_usuario" class="form-control form-lb__input" type="text" name="nombre_usuario"
                       value="{{ old('nombre_usuario') }}" required placeholder="Nombre de usuario">
            </div>

            <div class="form-lb__group">
                <label for="direccion_correo">Email <span style="color: #ff6b6b;">*</span></label>
                <input id="direccion_correo" class="form-control form-lb__input" type="email" name="direccion_correo"
                       value="{{ old('direccion_correo') }}" required placeholder="tu@email.com">
            </div>

            <div class="form-lb__group">
                <label for="contrasena">Contraseña <span style="color: #ff6b6b;">*</span></label>
                <input id="contrasena" class="form-control form-lb__input" type="password" name="contrasena"
                       required placeholder="••••••••">
            </div>

            @if(auth()->check() && auth()->user()->esAdmin())
                <div class="form-lb__group">
                    <label for="rol">Rol</label>
                    <select id="rol" class="form-select form-lb__select" name="rol">
                        <option value="usuario" {{ old('rol') === 'usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            @endif

            {{-- SEPARADOR --}}
            <hr style="border: none; border-top: 1px solid var(--line); margin: 24px 0;">
            <p style="color: rgba(255,255,255,.5); font-size: 13px; text-align: center; margin-bottom: 18px;">Información adicional (opcional)</p>

            <div class="form-lb__group">
                <label for="descripcion_perfil">Descripción de perfil</label>
                <textarea id="descripcion_perfil" class="form-control form-lb__textarea" name="descripcion_perfil"
                          placeholder="Descripción del perfil...">{{ old('descripcion_perfil') }}</textarea>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="calle">Calle</label>
                    <input id="calle" class="form-control form-lb__input" type="text" name="calle"
                           value="{{ old('calle') }}" placeholder="Dirección">
                </div>

                <div class="form-lb__group">
                    <label for="localidad">Localidad</label>
                    <input id="localidad" class="form-control form-lb__input" type="text" name="localidad"
                           value="{{ old('localidad') }}" placeholder="Ciudad">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="provincia">Provincia</label>
                    <input id="provincia" class="form-control form-lb__input" type="text" name="provincia"
                           value="{{ old('provincia') }}" placeholder="Provincia">
                </div>

                <div class="form-lb__group">
                    <label for="pais">País</label>
                    <input id="pais" class="form-control form-lb__input" type="text" name="pais"
                           value="{{ old('pais') }}" placeholder="España">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="codigo_postal">Código Postal</label>
                    <input id="codigo_postal" class="form-control form-lb__input" type="text" name="codigo_postal"
                           value="{{ old('codigo_postal') }}" placeholder="28001">
                </div>
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary" type="submit">Guardar</button>
                <a class="btn btn--ghost" href="{{ route('usuario.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
