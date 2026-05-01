@extends('layouts.master')

@section('title', 'Detalle Beat')

@section('content')
    <div class="collection">
        <div>
            <span class="badge">Beat</span>
            <h2>{{ $beat->titulo_beat }}</h2>
            <p class="muted">Género: {{ $beat->genero_musical ?? 'No especificado' }}</p>
            <p class="muted">Estado de ánimo: {{ $beat->estado_de_animo ?? '-' }}</p>
            <p class="price">{{ $beat->precio_base_licencia }} €</p>

            <div class="panel panel--dark" style="margin-top:14px;">
                <h3 style="margin-top:0;">View</h3>
                <audio controls class="audio-player-custom" style="width:100%; height: 44px; outline: none;">
                    <source src="{{ asset($beat->url_audio_previsualizacion ?? 'media/audio/demo.mp3') }}" type="audio/mpeg">
                </audio>
            </div>

            @if($beat->colecciones && $beat->colecciones->count())
                <div style="margin-top:18px;">
                    <h3>Colecciones</h3>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($beat->colecciones as $col)
                            <a href="{{ route('coleccion.detail', ['id' => $col->id]) }}"
                               class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;">
                                {{ $col->titulo_coleccion }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="collection__right" style="overflow:hidden;">
            <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                 alt="Portada {{ $beat->titulo_beat }}"
                 style="width:100%;height:100%;object-fit:cover;">
        </div>
    </div>

    @if(auth()->check() && !auth()->user()->esAdmin())
        <form action="{{ route('carrito.addBeat') }}" method="POST" class="add-to-cart-form">
            @csrf
            <input type="hidden" name="id" value="{{ $beat->id }}">
            <button class="btn btn--primary" type="submit">Añadir al carrito</button>
        </form>
    @else
        <p style="margin-top: 20px;">
            <a class="btn btn--ghost" href="{{ route('login') }}">
                Inicia sesión para comprar
            </a>
        </p>
    @endif

    {{-- ADMIN / PROPIETARIO --}}
    @if(auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $beat->id_usuario))
        <div class="admin-actions d-flex gap-2" style="margin-top: 15px;">
            <a class="btn btn--ghost" href="{{ route('beat.edit', $beat->id) }}">Editar</a>
            <a class="btn btn--ghost" style="color: #ff4d4d; border-color: rgba(255,77,77,.3);" href="{{ route('beat.delete', $beat->id) }}"
               onclick="return confirm('¿Seguro que quieres eliminar este beat?')">
                Eliminar
            </a>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Volver al listado</a>
    </div>
@endsection
