@extends('layouts.master')

@section('title', 'Conversación | LevelBeats')

@section('content')
<div class="messages-page messages-page--thread">
    <a href="{{ route('mensajes.index') }}" class="admin-back-link">← Volver a Mensajes</a>

    <header class="message-thread-head">
        <div class="message-thread-user">
            <span class="profile-avatar profile-avatar--message">
                @if($otroUsuario?->url_foto_perfil)
                    <img src="{{ asset($otroUsuario->url_foto_perfil) }}" alt="{{ $otroUsuario->nombre_usuario }}">
                @else
                    {{ strtoupper(substr($otroUsuario?->nombre_usuario ?? 'U', 0, 1)) }}
                @endif
            </span>
            <div>
                <span class="profiles-kicker">Conversación</span>
                <h1>{{ $otroUsuario->nombre_usuario ?? 'Usuario no disponible' }}</h1>
            </div>
        </div>
        @if($otroUsuario)
            <a class="btn btn--ghost" href="{{ route('perfiles.show', $otroUsuario) }}">Ver perfil</a>
        @endif
    </header>

    @if($errors->any())
        <div class="account-feedback account-feedback--error">{{ $errors->first() }}</div>
    @endif

    <section class="message-thread">
        @foreach($conversacion->mensajes as $mensaje)
            @php $propio = (int) $mensaje->emisor_id === auth()->id(); @endphp
            <article class="message-bubble {{ $propio ? 'is-own' : 'is-other' }}">
                <div>
                    <p>{{ $mensaje->cuerpo }}</p>
                    <time>{{ $mensaje->fecha_envio?->format('d/m/Y H:i') }}</time>
                </div>
            </article>
        @endforeach
    </section>

    <form class="message-compose" method="POST" action="{{ route('mensajes.send', $conversacion) }}">
        @csrf
        <textarea class="form-control form-lb__textarea" name="cuerpo" rows="3" maxlength="2000" placeholder="Escribe tu mensaje..." required></textarea>
        <button class="btn btn--primary" type="submit">Enviar</button>
    </form>
</div>
@endsection
