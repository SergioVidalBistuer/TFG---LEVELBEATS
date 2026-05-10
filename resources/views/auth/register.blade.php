@extends('layouts.master')

@section('title', 'Registro')

@section('content')
    <div class="form-lb">
        <h1>Crear Cuenta</h1>

        @if ($errors->any())
            <div class="form-lb__error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.save') }}">
            @csrf

            <div class="form-lb__group">
                <label for="nombre">Nombre de usuario <span style="color: #ff6b6b;">*</span></label>
                <input id="nombre" class="form-control form-lb__input" type="text" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required placeholder="Tu nombre artístico">
            </div>

            <div class="form-lb__group">
                <label for="email">Email <span style="color: #ff6b6b;">*</span></label>
                <input id="email" class="form-control form-lb__input" type="email" name="direccion_correo" value="{{ old('direccion_correo') }}" required placeholder="tu@email.com">
            </div>

            <div class="form-lb__group">
                <label for="password">Contraseña <span style="color: #ff6b6b;">*</span></label>
                <input id="password" class="form-control form-lb__input" type="password" name="contrasena" required placeholder="••••••••">
            </div>

            {{-- SEPARADOR --}}
            <hr style="border: none; border-top: 1px solid var(--line); margin: 24px 0;">
            <p style="color: rgba(255,255,255,.5); font-size: 13px; text-align: center; margin-bottom: 18px;">Información adicional (opcional)</p>

            <div class="form-lb__group">
                <label for="descripcion_perfil">Descripción de perfil</label>
                <textarea id="descripcion_perfil" class="form-control form-lb__textarea" name="descripcion_perfil" placeholder="Cuéntanos sobre ti, tu estilo musical...">{{ old('descripcion_perfil') }}</textarea>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="calle">Calle</label>
                    <input id="calle" class="form-control form-lb__input" type="text" name="calle" value="{{ old('calle') }}" placeholder="Tu dirección">
                </div>

                <div class="form-lb__group">
                    <label for="localidad">Localidad</label>
                    <input id="localidad" class="form-control form-lb__input" type="text" name="localidad" value="{{ old('localidad') }}" placeholder="Ciudad">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="provincia">Provincia</label>
                    <input id="provincia" class="form-control form-lb__input" type="text" name="provincia" value="{{ old('provincia') }}" placeholder="Provincia">
                </div>

                <div class="form-lb__group">
                    <label for="pais">País</label>
                    <input id="pais" class="form-control form-lb__input" type="text" name="pais" value="{{ old('pais') }}" placeholder="España">
                </div>
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary w-100" type="submit">Registrarse</button>
            </div>
        </form>

        <div class="form-lb__footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </div>
    </div>
@endsection
