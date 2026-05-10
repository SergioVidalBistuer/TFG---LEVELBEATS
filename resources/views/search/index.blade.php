@extends('layouts.master')

@section('title', 'Resultados de búsqueda | LevelBeats')

@section('content')
    @php
        $totalResultados = $beats->count() + $colecciones->count() + $servicios->count();
    @endphp

    <div class="section__head search-page__head">
        <div>
            <h2>Resultados de búsqueda</h2>
            <p class="muted" style="margin:6px 0 0;">
                @if($termino !== '')
                    Coincidencias para “{{ $termino }}”
                @else
                    Introduce un término en el buscador para encontrar beats, colecciones y servicios.
                @endif
            </p>
        </div>
        @if($termino !== '')
            <span class="search-count">{{ $totalResultados }} resultados</span>
        @endif
    </div>

    @if($termino === '')
        <div class="panel search-empty">
            <h3>Busca en LevelBeats</h3>
            <p class="muted">Puedes buscar por título de beat, colección o servicio desde el buscador superior.</p>
        </div>
    @elseif($totalResultados === 0)
        <div class="panel search-empty">
            <h3>No se encontraron resultados</h3>
            <p class="muted">No hay coincidencias para “{{ $termino }}”. Prueba con otro término o revisa el catálogo completo.</p>
            <div class="search-empty__actions">
                <a class="btn btn--ghost" href="{{ route('beat.index') }}">Ver beats</a>
                <a class="btn btn--ghost" href="{{ route('coleccion.index') }}">Ver colecciones</a>
                <a class="btn btn--ghost" href="{{ route('servicio.index') }}">Ver servicios</a>
            </div>
        </div>
    @else
        <div class="search-results">
            <section class="search-section">
                <div class="search-section__head">
                    <h3>Beats</h3>
                    <span>{{ $beats->count() }}</span>
                </div>
                @if($beats->count())
                    <div class="search-list">
                        @foreach($beats as $beat)
                            <a class="search-result" href="{{ route('beat.detail', $beat->id) }}">
                                <div class="search-result__media">
                                    <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}" alt="Portada {{ $beat->titulo_beat }}">
                                </div>
                                <div>
                                    <h4>{{ $beat->titulo_beat }}</h4>
                                    <p>{{ $beat->genero_musical ?? 'Sin género' }} · {{ $beat->usuario->nombre_usuario ?? 'Productor' }}</p>
                                </div>
                                <span class="btn btn--ghost">Ver detalle</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="search-section__empty">Sin beats coincidentes.</p>
                @endif
            </section>

            <section class="search-section">
                <div class="search-section__head">
                    <h3>Colecciones</h3>
                    <span>{{ $colecciones->count() }}</span>
                </div>
                @if($colecciones->count())
                    <div class="search-list">
                        @foreach($colecciones as $coleccion)
                            <a class="search-result" href="{{ route('coleccion.detail', $coleccion->id) }}">
                                <div class="search-result__media search-result__media--fallback">
                                    @if($coleccion->beats->first() && $coleccion->beats->first()->url_portada_beat)
                                        <img src="{{ asset($coleccion->beats->first()->url_portada_beat) }}" alt="Portada {{ $coleccion->titulo_coleccion }}">
                                    @else
                                        <span>{{ strtoupper(substr($coleccion->titulo_coleccion, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h4>{{ $coleccion->titulo_coleccion }}</h4>
                                    <p>{{ $coleccion->estilo_genero ?? 'Sin género' }} · {{ $coleccion->beats->count() }} beats</p>
                                </div>
                                <span class="btn btn--ghost">Ver detalle</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="search-section__empty">Sin colecciones coincidentes.</p>
                @endif
            </section>

            <section class="search-section">
                <div class="search-section__head">
                    <h3>Servicios</h3>
                    <span>{{ $servicios->count() }}</span>
                </div>
                @if($servicios->count())
                    <div class="search-list">
                        @foreach($servicios as $servicio)
                            <a class="search-result" href="{{ route('servicio.detail', $servicio->id) }}">
                                <div class="search-result__media search-result__media--fallback">
                                    <span>{{ strtoupper(substr($servicio->tipo_servicio ?? 'S', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h4>{{ $servicio->titulo_servicio }}</h4>
                                    <p>{{ ucfirst($servicio->tipo_servicio ?? 'Servicio') }} · {{ number_format($servicio->precio_servicio, 0) }} €</p>
                                </div>
                                <span class="btn btn--ghost">Ver servicio</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="search-section__empty">Sin servicios coincidentes.</p>
                @endif
            </section>
        </div>
    @endif
@endsection
