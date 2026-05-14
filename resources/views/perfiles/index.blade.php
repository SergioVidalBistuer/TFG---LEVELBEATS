@extends('layouts.master')

@section('title', 'Perfiles | LevelBeats')

@php
    $rolActivo = $rol && in_array($rol, ['productor', 'ingeniero'], true) ? $rol : 'todos';
@endphp

@section('content')
<div class="profiles-page">
    <header class="profiles-hero">
        <span class="profiles-kicker">Marketplace</span>
        <h1>Perfiles</h1>
        <p>Descubre productores e ingenieros activos en LevelBeats y conecta directamente con ellos.</p>
    </header>

    <section class="profiles-filters">
        <div class="profiles-tabs" aria-label="Filtrar perfiles por rol">
            <a class="{{ $rolActivo === 'todos' ? 'is-active' : '' }}" href="{{ route('perfiles.index', array_filter(['q' => $busqueda])) }}">Todos</a>
            <a class="{{ $rolActivo === 'productor' ? 'is-active' : '' }}" href="{{ route('perfiles.index', array_filter(['rol' => 'productor', 'q' => $busqueda])) }}">Productores</a>
            <a class="{{ $rolActivo === 'ingeniero' ? 'is-active' : '' }}" href="{{ route('perfiles.index', array_filter(['rol' => 'ingeniero', 'q' => $busqueda])) }}">Ingenieros</a>
        </div>

        <form class="profiles-search" method="GET" action="{{ route('perfiles.index') }}" autocomplete="off">
            @if($rolActivo !== 'todos')
                <input type="hidden" name="rol" value="{{ $rolActivo }}">
            @endif
            <input type="search" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre, bio o ciudad" aria-label="Buscar perfiles">
            <button class="btn btn--ghost" type="submit">Buscar</button>
        </form>
    </section>

    @unless($perfilPublicoDisponible)
        <div class="profiles-notice">
            El listado de perfiles necesita activar la columna <code>perfil_publico</code> en la base de datos.
        </div>
    @endunless

    @if($perfiles->isEmpty())
        <section class="profiles-empty">
            <h2>Todavía no hay perfiles públicos disponibles.</h2>
            <p>Cuando productores e ingenieros activen su perfil público, aparecerán aquí.</p>
        </section>
    @else
        <section class="profiles-grid">
            @foreach($perfiles as $perfil)
                @php
                    $roles = $perfil->roles->pluck('nombre_rol')->map(fn($r) => strtolower($r));
                    $rolPrincipal = $roles->contains('productor') ? 'Productor' : 'Ingeniero';
                    $ubicacion = collect([$perfil->localidad, $perfil->provincia, $perfil->pais])->filter()->implode(', ');
                @endphp
                <article class="profile-card">
                    <div class="profile-card__top">
                        <span class="profile-avatar profile-avatar--card">
                            @if($perfil->url_foto_perfil)
                                <img src="{{ asset($perfil->url_foto_perfil) }}" alt="{{ $perfil->nombre_usuario }}">
                            @else
                                {{ strtoupper(substr($perfil->nombre_usuario, 0, 1)) }}
                            @endif
                        </span>
                        <span class="profile-role">{{ $rolPrincipal }}</span>
                    </div>

                    <h2>{{ $perfil->nombre_usuario }}</h2>
                    <p>{{ $perfil->descripcion_perfil ?: 'Perfil profesional de LevelBeats.' }}</p>

                    <div class="profile-card__meta">
                        @if($ubicacion)
                            <span>{{ $ubicacion }}</span>
                        @endif
                        @if($roles->contains('productor'))
                            <span>{{ $perfil->beats_publicados_count }} beats publicados</span>
                        @endif
                        @if($roles->contains('ingeniero'))
                            <span>{{ $perfil->servicios_activos_count }} servicios activos</span>
                        @endif
                    </div>

                    <a class="btn btn--ghost profile-card__link" href="{{ route('perfiles.show', $perfil) }}">Ver perfil</a>
                </article>
            @endforeach
        </section>
    @endif
</div>
@endsection
