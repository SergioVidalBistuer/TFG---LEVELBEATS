@extends('layouts.master')

@section('title', 'Mensajes | LevelBeats')

@section('content')
<div class="messages-page">
    <header class="messages-head">
        <div>
            <span class="profiles-kicker">Mi Área</span>
            <h1>Mensajes</h1>
            <p>Conversaciones directas con perfiles de productores e ingenieros.</p>
        </div>
    </header>

    <section class="messages-list">
        @if($conversaciones->isEmpty())
            <div class="profiles-empty">
                <h2>No tienes conversaciones todavía.</h2>
                <p>Explora perfiles públicos para iniciar una conversación.</p>
                <a class="btn btn--primary" href="{{ route('perfiles.index') }}">Ver perfiles</a>
            </div>
        @else
            @foreach($conversaciones as $conversacion)
                @php
                    $otro = $conversacion->otroUsuario(auth()->id());
                    $ultimo = $conversacion->ultimoMensaje;
                    $noLeidos = $conversacion->mensajes()
                        ->where('emisor_id', '<>', auth()->id())
                        ->where('leido', false)
                        ->count();
                @endphp
                <a class="conversation-row" href="{{ route('mensajes.show', $conversacion) }}">
                    <span class="profile-avatar profile-avatar--message">
                        @if($otro?->url_foto_perfil)
                            <img src="{{ asset($otro->url_foto_perfil) }}" alt="{{ $otro->nombre_usuario }}">
                        @else
                            {{ strtoupper(substr($otro?->nombre_usuario ?? 'U', 0, 1)) }}
                        @endif
                    </span>
                    <span class="conversation-row__body">
                        <strong>{{ $otro->nombre_usuario ?? 'Usuario no disponible' }}</strong>
                        <small>{{ $ultimo?->cuerpo ?? 'Conversación iniciada.' }}</small>
                    </span>
                    <span class="conversation-row__meta">
                        @if($ultimo?->fecha_envio)
                            <time>{{ $ultimo->fecha_envio->format('d/m/Y H:i') }}</time>
                        @endif
                        @if($noLeidos > 0)
                            <span class="message-unread">{{ $noLeidos }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        @endif
    </section>
</div>
@endsection
