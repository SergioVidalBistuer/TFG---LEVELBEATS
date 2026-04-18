@extends('layouts.master')

@section('title', 'Mi Perfil')

@section('content')

    {{-- CABECERA DEL PERFIL --}}
    <div class="profile-header">
        <div class="profile-avatar">
            @if($usuario->url_foto_perfil)
                <img src="{{ asset($usuario->url_foto_perfil) }}" alt="{{ $usuario->nombre_usuario }}">
            @else
                {{ strtoupper(substr($usuario->nombre_usuario, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <h1>{{ $usuario->nombre_usuario }}</h1>
            <p>{{ $usuario->direccion_correo }}</p>
            <p style="margin-top:4px;">
                <span class="badge" style="font-size: 11px;">{{ ucfirst($usuario->rol) }}</span>
                @if($usuario->verificacion_completada)
                    <span style="color: #00e676; font-size: 12px; margin-left: 8px;">✓ Verificado</span>
                @endif
            </p>
        </div>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="profile-stats">
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->beats->count() }}</span>
            <span class="profile-stat__label">Beats</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->colecciones->count() }}</span>
            <span class="profile-stat__label">Colecciones</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->comprasComoComprador->count() }}</span>
            <span class="profile-stat__label">Compras</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') : '-' }}</span>
            <span class="profile-stat__label">Miembro desde</span>
        </div>
    </div>

    {{-- INFO PERSONAL --}}
    @if($usuario->descripcion_perfil || $usuario->localidad || $usuario->pais)
        <div class="panel panel--dark" style="padding: 24px; margin-bottom: 32px;">
            <h3 style="margin-top: 0;">Sobre mí</h3>
            @if($usuario->descripcion_perfil)
                <p>{{ $usuario->descripcion_perfil }}</p>
            @endif
            @if($usuario->localidad || $usuario->provincia || $usuario->pais)
                <p style="color: rgba(255,255,255,.5); font-size: 14px;">
                    📍 {{ collect([$usuario->localidad, $usuario->provincia, $usuario->pais])->filter()->implode(', ') }}
                </p>
            @endif
        </div>
    @endif

    {{-- MIS BEATS --}}
    @if($usuario->beats->count())
        <h2>Mis Beats</h2>
        <div class="grid grid--4">
            @foreach($usuario->beats as $beat)
                <article class="card">
                    <div class="card__media">
                        <img src="{{ asset($beat->url_portada_beat) }}"
                             alt="Portada {{ $beat->titulo_beat }}"
                             style="width:100%;height:128px;object-fit:cover;">
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                        <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>
                        <div class="card__foot">
                            <span class="price">{{ $beat->precio_base_licencia }} €</span>
                            <div class="card__actions">
                                <a class="btn btn--ghost" href="{{ route('beat.detail', $beat->id) }}">Ver</a>
                                <a class="btn btn--ghost" href="{{ route('beat.edit', $beat->id) }}">Editar</a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- MIS COLECCIONES --}}
    @if($usuario->colecciones->count())
        <h2 style="margin-top: 32px;">Mis Colecciones</h2>
        <div class="grid grid--4">
            @foreach($usuario->colecciones as $coleccion)
                <article class="card">
                    <div class="card__media">
                        @if($coleccion->beats->first() && $coleccion->beats->first()->url_portada_beat)
                            <img src="{{ asset($coleccion->beats->first()->url_portada_beat) }}"
                                 alt="Portada {{ $coleccion->titulo_coleccion }}"
                                 style="width:100%;height:128px;object-fit:cover;">
                        @else
                            <div style="width:100%;height:128px;background:linear-gradient(135deg, var(--primary), #1a1a2e);display:flex;align-items:center;justify-content:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $coleccion->titulo_coleccion }}</h3>
                        <p class="card__meta">{{ $coleccion->beats->count() }} beats</p>
                        <div class="card__foot">
                            <span class="price">{{ $coleccion->precio ?? '—' }} €</span>
                            <div class="card__actions">
                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', $coleccion->id) }}">Ver</a>
                                @if(session('rol') === 'admin')
                                    <a class="btn btn--ghost" href="{{ route('coleccion.edit', $coleccion->id) }}">Editar</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

@endsection
