@extends('layouts.master')

@section('title', 'Mis productos')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">Mi Área</p>
            <h1>Mis productos</h1>
            <p class="muted">Biblioteca personal de beats, colecciones, licencias y servicios contratados en LevelBeats.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Explorar catálogo</a>
    </div>

    @if($beatsComprados->isEmpty() && $colecciones->isEmpty() && $serviciosContratados->isEmpty())
        <section class="studio-panel">
            <div class="studio-empty">
                <h2>Todavía no tienes productos comprados</h2>
                <p class="muted">Cuando compres beats, colecciones o contrates servicios, aparecerán aquí para consultarlos.</p>
                <a class="btn btn--primary" href="{{ route('beat.index') }}">Explorar beats</a>
            </div>
        </section>
    @else
        <section class="area-section">
            <div class="area-section__head">
                <h2>Beats comprados</h2>
                <span>{{ $beatsComprados->count() }} disponibles</span>
            </div>

            @if($bibliotecaBeats->isNotEmpty())
                <div class="grid grid--4">
                    @foreach($bibliotecaBeats as $entrada)
                        @php
                            $beat = $entrada['beat'];
                            $detalle = $entrada['detalle'];
                            $licenciaNombre = $detalle?->nombre_licencia_snapshot ?? 'Licencia no registrada';
                            $formato = $detalle?->formato_incluido_snapshot ?? 'No registrado';
                            $precioPagado = $detalle ? (float) $detalle->precio_final : (float) $beat->precio_base_licencia;
                        @endphp
                        @if($beat)
                            <article class="card product-library-card">
                                <div class="card__media">
                                    <img src="{{ \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                         alt="Portada {{ $beat->titulo_beat }}"
                                         loading="lazy"
                                         decoding="async">
                                </div>
                                <div class="card__body">
                                    <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                                    <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>
                                    <p class="card__meta">{{ $entrada['origen'] }}</p>
                                    <span class="license-chip">{{ $licenciaNombre }}</span>
                                    <p class="card__meta">Formato: {{ $formato }}</p>
                                    <div class="card__foot">
                                        <span class="price">{{ number_format($precioPagado, 2, ',', '.') }} €</span>
                                    </div>
                                    <div class="area-card-actions">
                                        <a class="btn btn--ghost" href="{{ route('beat.detail', $beat->id) }}">Ver detalle</a>
                                        <a class="btn btn--primary" href="{{ route('usuario.productos.beats.descargar', $beat->id) }}">Descargar</a>
                                        @if($detalle)
                                            <a class="btn btn--ghost" href="{{ route('usuario.productos.licencia.ver', $detalle->id) }}" target="_blank" rel="noopener">Licencia PDF</a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            @elseif($beatsComprados->isNotEmpty())
                <div class="grid grid--4">
                    @foreach($beatsComprados as $beat)
                        <article class="card product-library-card">
                            <div class="card__media">
                                <img src="{{ \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                     alt="Portada {{ $beat->titulo_beat }}"
                                     loading="lazy"
                                     decoding="async">
                            </div>
                            <div class="card__body">
                                <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                                <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>
                                <span class="license-chip">Licencia no registrada</span>
                                <div class="card__foot">
                                    <span class="price">{{ number_format($beat->precio_base_licencia, 2, ',', '.') }} €</span>
                                </div>
                                <div class="area-card-actions">
                                    <a class="btn btn--ghost" href="{{ route('beat.detail', $beat->id) }}">Ver detalle</a>
                                    <a class="btn btn--primary" href="{{ route('usuario.productos.beats.descargar', $beat->id) }}">Descargar</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="studio-panel">
                    <div class="studio-empty studio-empty--compact">
                        <p class="muted">No tienes beats comprados todavía.</p>
                    </div>
                </div>
            @endif
        </section>

        <section class="area-section">
            <div class="area-section__head">
                <h2>Colecciones compradas</h2>
                <span>{{ $colecciones->count() }} disponibles</span>
            </div>

            @if($detallesColeccion->isNotEmpty() || $coleccionesLegacy->isNotEmpty())
                <div class="area-collection-list">
                    @foreach($detallesColeccion as $detalle)
                        @php
                            $coleccion = $coleccionesDetalle->get($detalle->id_producto);
                            $licenciaNombre = $detalle->nombre_licencia_snapshot ?? 'Licencia no registrada';
                        @endphp
                        @if($coleccion)
                            <article class="area-collection">
                                <div class="area-collection__media">
                                    <img src="{{ \App\Support\Imagenes::portada($coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                         alt="Portada {{ $coleccion->titulo_coleccion }}"
                                         loading="lazy"
                                         decoding="async">
                                </div>

                                <div class="area-collection__body">
                                    <div class="area-collection__top">
                                        <div>
                                            <h3>{{ $coleccion->titulo_coleccion }}</h3>
                                            <p class="muted">{{ $coleccion->estilo_genero ?? 'Sin género' }} · {{ $coleccion->beats->count() }} beats incluidos</p>
                                            <span class="license-chip">{{ $licenciaNombre }}</span>
                                            <p class="card__meta">Formato: {{ $detalle->formato_incluido_snapshot ?? 'No registrado' }}</p>
                                        </div>
                                        <span class="price">{{ number_format($detalle->precio_final, 2, ',', '.') }} €</span>
                                    </div>

                                    <div class="area-card-actions">
                                        <a class="btn btn--ghost" href="{{ route('coleccion.detail', $coleccion->id) }}">Ver detalle</a>
                                        <a class="btn btn--ghost" href="{{ route('usuario.productos.licencia.ver', $detalle->id) }}" target="_blank" rel="noopener">Licencia PDF</a>
                                    </div>

                                    <div class="area-track-list">
                                        @foreach($coleccion->beats as $beat)
                                            <div class="area-track">
                                                <div>
                                                    <strong>{{ $beat->titulo_beat }}</strong>
                                                    <span>{{ $beat->genero_musical ?? '-' }}</span>
                                                </div>
                                                <a class="btn btn--primary btn-sm" href="{{ route('usuario.productos.beats.descargar', $beat->id) }}">Descargar</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach

                    @foreach($coleccionesLegacy as $coleccion)
                        <article class="area-collection">
                            <div class="area-collection__media">
                                <img src="{{ \App\Support\Imagenes::portada($coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                     alt="Portada {{ $coleccion->titulo_coleccion }}"
                                     loading="lazy"
                                     decoding="async">
                            </div>

                            <div class="area-collection__body">
                                <div class="area-collection__top">
                                    <div>
                                        <h3>{{ $coleccion->titulo_coleccion }}</h3>
                                        <p class="muted">{{ $coleccion->estilo_genero ?? 'Sin género' }} · {{ $coleccion->beats->count() }} beats incluidos</p>
                                        <span class="license-chip">Licencia no registrada</span>
                                        <p class="card__meta">Formato: No registrado</p>
                                    </div>
                                    <span class="price">{{ number_format($coleccion->precio, 2, ',', '.') }} €</span>
                                </div>

                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', $coleccion->id) }}">Ver detalle</a>

                                <div class="area-track-list">
                                    @foreach($coleccion->beats as $beat)
                                        <div class="area-track">
                                            <div>
                                                <strong>{{ $beat->titulo_beat }}</strong>
                                                <span>{{ $beat->genero_musical ?? '-' }}</span>
                                            </div>
                                            <a class="btn btn--primary btn-sm" href="{{ route('usuario.productos.beats.descargar', $beat->id) }}">Descargar</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif($colecciones->isNotEmpty())
                <div class="area-collection-list">
                    @foreach($colecciones as $coleccion)
                        <article class="area-collection">
                            <div class="area-collection__media">
                                <img src="{{ \App\Support\Imagenes::portada($coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                     alt="Portada {{ $coleccion->titulo_coleccion }}"
                                     loading="lazy"
                                     decoding="async">
                            </div>

                            <div class="area-collection__body">
                                <div class="area-collection__top">
                                    <div>
                                        <h3>{{ $coleccion->titulo_coleccion }}</h3>
                                        <p class="muted">{{ $coleccion->estilo_genero ?? 'Sin género' }} · {{ $coleccion->beats->count() }} beats incluidos</p>
                                        <span class="license-chip">Licencia no registrada</span>
                                    </div>
                                    <span class="price">{{ number_format($coleccion->precio, 2, ',', '.') }} €</span>
                                </div>

                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', $coleccion->id) }}">Ver detalle</a>

                                <div class="area-track-list">
                                    @foreach($coleccion->beats as $beat)
                                        <div class="area-track">
                                            <div>
                                                <strong>{{ $beat->titulo_beat }}</strong>
                                                <span>{{ $beat->genero_musical ?? '-' }}</span>
                                            </div>
                                            <a class="btn btn--primary btn-sm" href="{{ route('usuario.productos.beats.descargar', $beat->id) }}">Descargar</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="studio-panel">
                    <div class="studio-empty studio-empty--compact">
                        <p class="muted">No tienes colecciones compradas todavía.</p>
                    </div>
                </div>
            @endif
        </section>

        <section class="area-section">
            <div class="area-section__head">
                <h2>Servicios contratados</h2>
                <span>{{ $serviciosContratados->count() }} disponibles</span>
            </div>

            @if($serviciosContratados->isNotEmpty())
                <div class="area-service-list">
                    @foreach($serviciosContratados as $entrada)
                        @php
                            $servicio = $entrada['servicio'];
                            $compra = $entrada['compra'];
                            $proyecto = $entrada['proyecto'];
                            $estadoProyecto = [
                                'pendiente_aceptacion_ingeniero' => 'Pendiente de aceptación',
                                'pendiente_pago_cliente' => 'Pendiente de pago',
                                'pendiente_archivos' => 'Pendiente de archivos',
                                'archivos_recibidos' => 'Archivos recibidos',
                                'en_proceso' => 'En proceso',
                                'en_revision' => 'En revisión',
                                'entregado' => 'Entregado',
                                'cerrado' => 'Cerrado',
                                'cancelado' => 'Cancelado',
                            ][$proyecto?->estado_proyecto] ?? 'Servicio comprado';
                        @endphp
                        <article class="area-service-item">
                            <div>
                                <span class="studio-eyebrow">Servicio contratado</span>
                                <h3>{{ $servicio->titulo_servicio }}</h3>
                                <p class="muted">Ingeniero: {{ $servicio->usuario->nombre_usuario ?? 'Desconocido' }}</p>
                                <p class="card__meta">Compra: {{ $compra->fecha_compra ? \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div class="area-service-item__meta">
                                <span class="price">{{ number_format($compra->importe_total, 2, ',', '.') }} €</span>
                                <span class="studio-badge studio-badge--public">{{ $estadoProyecto }}</span>
                                <div class="area-card-actions">
                                    @if($proyecto)
                                        <a class="btn btn--primary btn-sm" href="{{ route('usuario.encargos.detail', $proyecto->id) }}">Ver encargo</a>
                                    @endif
                                    <a class="btn btn--ghost btn-sm" href="{{ route('servicio.detail', $servicio->id) }}">Ver servicio</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="studio-panel">
                    <div class="studio-empty studio-empty--compact">
                        <p class="muted">No tienes servicios contratados todavía.</p>
                    </div>
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
