@extends('layouts.master')

@section('title', 'Iniciar Sesión')

@section('content')
    <div class="form-lb form-lb--narrow">
        <h1>Iniciar Sesión</h1>

        @if ($errors->has('login'))
            <div class="form-lb__error">{{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-lb__group">
                <label for="email">Email</label>
                <input id="email" class="form-control form-lb__input" type="email" name="direccion_correo" value="{{ old('direccion_correo') }}" required placeholder="tu@email.com">
            </div>

            <div class="form-lb__group">
                <label for="password">Contraseña</label>
                <input id="password" class="form-control form-lb__input" type="password" name="contrasena" required placeholder="••••••••">
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary w-100" type="submit">Entrar</button>
            </div>
        </form>

        <div class="form-lb__footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
        </div>
    </div>
@endsection
