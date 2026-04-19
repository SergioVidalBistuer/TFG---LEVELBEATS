@extends('layouts.master')

@section('title', 'Listado de Colecciones')
@section('hero')
    <section class="lb-hero">
        <div class="lb-hero__inner container">
            <img src="{{ asset('media/img/LB-09.png') }}" class="lb-hero__logo" alt="Level Beats">
        </div>
    </section>
@endsection
@section('content')
    <div class="section__head">
        <h2>Colecciones</h2>

        @if(session()->has('usuario_id'))
            <a class="btn btn--primary" href="{{ route('coleccion.create') }}">Crear Colección</a>
        @endif
    </div>

    <div class="grid grid--4">
        @foreach($colecciones as $coleccion)
            <article class="card">
                <div class="card__media">
                    @if($coleccion->beats->first() && $coleccion->beats->first()->url_portada_beat)
                        <img src="{{ asset($coleccion->beats->first()->url_portada_beat) }}"
                             alt="Portada {{ $coleccion->titulo_coleccion }}"
                             style="width:100%;height:128px;object-fit:cover;">
                    @else
                        <div style="width:100%;height:128px;background:linear-gradient(135deg, var(--primary), #1a1a2e);display:flex;align-items:center;justify-content:center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </div>
                    @endif
                </div>

                <div class="card__body">
                    <h3 class="card__title">{{ $coleccion->titulo_coleccion }}</h3>
                    <p class="card__meta">Tipo: {{ $coleccion->tipo_coleccion ?? '-' }}</p>
                    <p class="card__meta">Género: {{ $coleccion->estilo_genero ?? '-' }}</p>
                    <p class="card__meta" style="font-size:12px; opacity:.6;">{{ $coleccion->beats->count() }} beats</p>

                    <div class="card__foot">
                        <div class="card__actions">
                            <a class="btn btn--ghost" href="{{ route('coleccion.detail', ['id' => $coleccion->id]) }}">Ver</a>

                            @if(session('rol') === 'admin')
                                <a class="btn btn--ghost" href="{{ route('coleccion.edit', ['id' => $coleccion->id]) }}">Editar</a>
                                <a class="btn btn--ghost" href="{{ route('coleccion.delete', ['id' => $coleccion->id]) }}"
                                    onclick="return confirm('¿Seguro que quieres borrar esta colección?')">
                                    Eliminar
                                </a>
                            @endif
                        </div>
                    </div>


                </div>
            </article>
        @endforeach
    </div>
    <div style="margin-top:18px;">
        {{ $colecciones->links('pagination::bootstrap-5') }}
    </div>
@endsection
