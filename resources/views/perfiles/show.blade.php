@extends('layouts.master')

@section('title', $usuario->nombre_usuario . ' | Perfiles LevelBeats')

@php
    $roles = $usuario->roles->pluck('nombre_rol')->map(fn($r) => strtolower($r));
    $ubicacion = collect([$usuario->localidad, $usuario->provincia, $usuario->pais])->filter()->implode(', ');
@endphp

@section('content')
<div class="profile-public-page">
    <a href="{{ route('perfiles.index') }}" class="admin-back-link">← Volver a Perfiles</a>

    <header class="profile-public-hero">
        <div class="profile-public-identity">
            <span class="profile-avatar profile-avatar--hero">
                @if($usuario->url_foto_perfil)
                    <img src="{{ asset($usuario->url_foto_perfil) }}" alt="{{ $usuario->nombre_usuario }}">
                @else
                    {{ strtoupper(substr($usuario->nombre_usuario, 0, 1)) }}
                @endif
            </span>
            <div>
                <span class="profiles-kicker">Perfil público</span>
                <h1>{{ $usuario->nombre_usuario }}</h1>
                <div class="profile-role-row">
                    @if($roles->contains('productor'))
                        <span class="profile-role">Productor</span>
                    @endif
                    @if($roles->contains('ingeniero'))
                        <span class="profile-role">Ingeniero</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-public-actions">
            @auth
                @if(auth()->id() === $usuario->id)
                    <span class="profile-self-note">Este es tu perfil</span>
                @else
                    <form method="POST" action="{{ route('mensajes.start', $usuario) }}">
                        @csrf
                        <button class="btn btn--primary" type="submit">Enviar mensaje</button>
                    </form>
                @endif
            @else
                <a class="btn btn--primary" href="{{ route('login') }}">Inicia sesión para enviar mensaje</a>
            @endauth
        </div>
    </header>

    <section class="profile-public-info">
        <article class="profile-info-panel">
            <h2>Sobre el perfil</h2>
            <p>{{ $usuario->descripcion_perfil ?: 'Este usuario todavía no ha añadido una descripción pública.' }}</p>
        </article>
        <article class="profile-info-panel">
            <h2>Datos</h2>
            <dl>
                <div>
                    <dt>Ubicación</dt>
                    <dd>{{ $ubicacion ?: 'No indicada' }}</dd>
                </div>
                <div>
                    <dt>Beats publicados</dt>
                    <dd>{{ $beats->count() }}</dd>
                </div>
                <div>
                    <dt>Servicios activos</dt>
                    <dd>{{ $servicios->count() }}</dd>
                </div>
            </dl>
        </article>
    </section>

    @if($roles->contains('productor'))
        <section class="profile-section">
            <div class="profile-section__head">
                <h2>Beats publicados</h2>
            </div>
            @if($beats->isEmpty())
                <div class="profiles-empty profiles-empty--compact">No hay beats públicos disponibles.</div>
            @else
                <div class="profile-product-grid">
                    @foreach($beats as $beat)
                        <a class="profile-product-card" href="{{ route('beat.detail', $beat->id) }}">
                            <span class="profile-product-card__media">
                                <img src="{{ \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                     alt="Portada {{ $beat->titulo_beat }}"
                                     loading="lazy"
                                     decoding="async">
                            </span>
                            <strong>{{ $beat->titulo_beat }}</strong>
                            <small>{{ $beat->genero_musical ?? 'Beat' }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="profile-section">
            <div class="profile-section__head">
                <h2>Colecciones</h2>
            </div>
            @if($colecciones->isEmpty())
                <div class="profiles-empty profiles-empty--compact">No hay colecciones públicas disponibles.</div>
            @else
                <div class="profile-product-grid">
                    @foreach($colecciones as $coleccion)
                        <a class="profile-product-card" href="{{ route('coleccion.detail', $coleccion->id) }}">
                            <span class="profile-product-card__media">
                                <img src="{{ \App\Support\Imagenes::portada($coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                     alt="Portada {{ $coleccion->titulo_coleccion }}"
                                     loading="lazy"
                                     decoding="async">
                            </span>
                            <strong>{{ $coleccion->titulo_coleccion }}</strong>
                            <small>{{ ucfirst($coleccion->tipo_coleccion ?? 'Colección') }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if($roles->contains('ingeniero'))
        <section class="profile-section">
            <div class="profile-section__head">
                <h2>Servicios activos</h2>
            </div>
            @if($servicios->isEmpty())
                <div class="profiles-empty profiles-empty--compact">No hay servicios activos disponibles.</div>
            @else
                <div class="profile-product-grid">
                    @foreach($servicios as $servicio)
                        <a class="profile-product-card" href="{{ route('servicio.detail', $servicio->id) }}">
                            <span class="profile-product-card__media">
                                @if($servicio->portada_url)
                                    <img src="{{ \App\Support\Imagenes::portada($servicio->portada_url) }}"
                                         alt="Portada {{ $servicio->titulo_servicio }}"
                                         loading="lazy"
                                         decoding="async">
                                @else
                                    <span>{{ strtoupper(substr($servicio->titulo_servicio, 0, 1)) }}</span>
                                @endif
                            </span>
                            <strong>{{ $servicio->titulo_servicio }}</strong>
                            <small>{{ ucfirst($servicio->tipo_servicio ?? 'Servicio') }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
