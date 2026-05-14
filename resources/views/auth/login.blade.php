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

        <div class="auth-social-divider">
            <span>o continúa con</span>
        </div>

        <a class="btn auth-google-btn" href="{{ route('auth.google.redirect') }}">
            <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c10 0 19-7.3 19-20 0-1.2-.1-2.3-.4-3.5z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/>
                <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.5-5.3l-6.2-5.2C29.3 35.1 26.8 36 24 36c-5.3 0-9.7-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.2-4.1 5.5l6.2 5.2C36.9 39.2 43 34.5 43 24c0-1.2-.1-2.3-.4-3.5z"/>
            </svg>
            Continuar con Google
        </a>

        <div class="form-lb__footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
        </div>
    </div>
@endsection
