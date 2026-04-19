@extends('layouts.master')

@section('title', 'Listado de Beats')
@section('hero')
    <section class="lb-hero">
        <div class="lb-hero__inner container">
            <img src="{{ asset('media/img/LB-09.png') }}" class="lb-hero__logo" alt="Level Beats">
        </div>
    </section>
@endsection
@section('content')
    <div class="section__head">
        <h2>Beats</h2>

        @if(auth()->check() && auth()->user()->tieneSuscripcionActiva('productor'))
            <a class="btn btn--primary" href="{{ route('studio.beats.create') }}">Crear Beat</a>
        @endif
    </div>

    <div class="grid grid--4">
        @foreach($beats as $beat)
            <article class="card">
                <div class="card__media">
                    <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                         alt="Portada {{ $beat->titulo_beat }}"
                         style="width:100%;height:128px;object-fit:cover;">
                </div>

                <div class="card__body">
                    <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                    <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>

                    <div class="card__foot">
                        <span class="price">{{ $beat->precio_base_licencia }} €</span>

                        <div class="card__actions">
                            <a class="btn btn--ghost" href="{{ route('beat.detail', ['id' => $beat->id]) }}">Ver</a>

                            @if(auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $beat->id_usuario))
                                <a class="btn btn--ghost" href="{{ route('beat.edit', ['id' => $beat->id]) }}">Editar</a>
                                <a class="btn btn--ghost"href="{{ route('beat.delete', ['id' => $beat->id]) }}"
                                    onclick="return confirm('¿Seguro que quieres borrar este beat?')">
                                    Eliminar
                                </a>
                            @endif
                        </div>
                    </div>

                    <div style="margin-top: auto;">
                        @if(auth()->check())
                            @if(!auth()->user()->esAdmin())
                                <form action="{{ route('carrito.addBeat') }}" method="POST" style="margin-top:12px;display:flex;gap:10px;align-items:center;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $beat->id }}">
                                    <button class="btn btn--primary w-100" type="submit">Añadir al carrito</button>
                                </form>
                            @endif
                        @else
                            <p style="margin-bottom: 0; padding-top: 12px; text-align: center;">
                                <a href="{{ route('login') }}" class="btn btn--ghost w-100">
                                    Inicia sesión para comprar
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    <div style="margin-top:18px;">
        {{ $beats->links('pagination::bootstrap-5') }}
    </div>
@endsection
