@extends('layouts.master')

@section('title', 'Mis Guardados – LevelBeats')

@section('hero')
    <section class="lb-hero lb-hero--guardados">
        <div class="lb-hero__inner container">
            <h1 class="lb-hero__title">Mis Guardados</h1>
        </div>
    </section>
@endsection

@section('content')

<style>
    /* ===== GUARDADOS PAGE ===== */
    .guardados-section {
        margin-bottom: 48px;
    }

    .guardados-section__head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(169,0,239,0.2);
    }

    .guardados-section__head h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .guardados-section__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(169,0,239,0.18);
        color: #D26BFF;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
    }

    .guardados-section__icon {
        opacity: .65;
    }

    .guardados-empty {
        padding: 32px;
        text-align: center;
        background: rgba(255,255,255,0.03);
        border: 1px dashed rgba(255,255,255,0.1);
        border-radius: 14px;
        color: rgba(255,255,255,0.4);
        font-size: 14px;
    }

    /* Botón quitar de guardados en la página de guardados */
    .btn-guardado-quitar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255,100,100,0.3);
        background: transparent;
        color: rgba(255,120,120,0.8);
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
        font-family: inherit;
        font-weight: 500;
        text-decoration: none;
    }

    .btn-guardado-quitar:hover {
        background: rgba(255,60,60,0.08);
        border-color: rgba(255,80,80,0.55);
        color: #ff7b7b;
    }

    .guardados-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 20px;
    }
</style>

{{-- =========================================================
     SECCIÓN BEATS
     ========================================================= --}}
<section class="guardados-section" id="guardados-beats">
    <div class="guardados-section__head">
        <svg class="guardados-section__icon" width="22" height="22" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <path d="M9 18V5l12-2v13"/>
            <circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>
        </svg>
        <h2>Beats guardados</h2>
        <span class="guardados-section__count">{{ $beats->count() }}</span>
    </div>

    @if($beats->isEmpty())
        <div class="guardados-empty">
            Aún no tienes beats guardados.
            <a href="{{ route('beat.index') }}" class="btn btn--ghost" style="display:inline-flex;margin-top:12px;font-size:13px;">
                Explorar beats
            </a>
        </div>
    @else
        <div class="guardados-grid">
            @foreach($beats as $guardado)
                @php $beat = $guardado->guardable; @endphp
                <article class="card card--clickable saved-card"
                         data-card-link="{{ route('beat.detail', ['id' => $beat->id]) }}"
                         role="link" tabindex="0"
                         aria-label="Ver detalle de {{ $beat->titulo_beat }}">
                    <div class="card__media">
                        <img src="{{ \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                             alt="Portada {{ $beat->titulo_beat }}"
                             loading="lazy"
                             decoding="async">
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                        <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>

                        <div class="card__foot saved-card__foot">
                            <span class="price saved-card__price">{{ number_format($beat->precio_base_licencia, 2, ',', '.') }} €</span>
                            <div class="card__actions saved-card__actions">
                                <a class="btn btn--ghost" href="{{ route('beat.detail', ['id' => $beat->id]) }}">Ver beat</a>

                                <form method="POST" action="{{ route('guardados.eliminar', ['tipo' => 'beat', 'id' => $beat->id]) }}"
                                      style="margin:0;" onsubmit="return confirm('¿Quitar de guardados?')">
                                    @csrf
                                    <button type="submit" class="btn-guardado-quitar" title="Quitar de guardados">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                                        </svg>
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

{{-- =========================================================
     SECCIÓN COLECCIONES
     ========================================================= --}}
<section class="guardados-section" id="guardados-colecciones">
    <div class="guardados-section__head">
        <svg class="guardados-section__icon" width="22" height="22" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        <h2>Colecciones guardadas</h2>
        <span class="guardados-section__count">{{ $colecciones->count() }}</span>
    </div>

    @if($colecciones->isEmpty())
        <div class="guardados-empty">
            Aún no tienes colecciones guardadas.
            <a href="{{ route('coleccion.index') }}" class="btn btn--ghost" style="display:inline-flex;margin-top:12px;font-size:13px;">
                Explorar colecciones
            </a>
        </div>
    @else
        <div class="guardados-grid">
            @foreach($colecciones as $guardado)
                @php
                    $col       = $guardado->guardable;
                    $numBeats  = $col->beats->count();
                @endphp
                <article class="card card--clickable saved-card"
                         data-card-link="{{ route('coleccion.detail', ['id' => $col->id]) }}"
                         role="link" tabindex="0"
                         aria-label="Ver detalle de {{ $col->titulo_coleccion }}">
                    <div class="card__media">
                        <img src="{{ \App\Support\Imagenes::portada($col->portada_url ?? $col->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                             alt="Portada {{ $col->titulo_coleccion }}"
                             loading="lazy"
                             decoding="async">
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $col->titulo_coleccion }}</h3>
                        <p class="card__meta">Género: {{ $col->estilo_genero ?? '-' }}</p>
                        <p class="card__meta" style="font-size:12px;opacity:.55;">{{ $numBeats }} beats incluidos</p>

                        <div class="card__foot saved-card__foot">
                            <span class="price saved-card__price">{{ number_format($col->precio, 2, ',', '.') }} €</span>
                            <div class="card__actions saved-card__actions">
                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', ['id' => $col->id]) }}">Ver colección</a>

                                <form method="POST" action="{{ route('guardados.eliminar', ['tipo' => 'coleccion', 'id' => $col->id]) }}"
                                      style="margin:0;" onsubmit="return confirm('¿Quitar de guardados?')">
                                    @csrf
                                    <button type="submit" class="btn-guardado-quitar" title="Quitar de guardados">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                                        </svg>
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

{{-- =========================================================
     SECCIÓN SERVICIOS
     ========================================================= --}}
<section class="guardados-section" id="guardados-servicios">
    <div class="guardados-section__head">
        <svg class="guardados-section__icon" width="22" height="22" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <path d="M3 6h18M3 12h18M3 18h18"/>
            <circle cx="7" cy="6" r="2" fill="currentColor" stroke="none"/>
            <circle cx="17" cy="12" r="2" fill="currentColor" stroke="none"/>
            <circle cx="10" cy="18" r="2" fill="currentColor" stroke="none"/>
        </svg>
        <h2>Servicios guardados</h2>
        <span class="guardados-section__count">{{ $servicios->count() }}</span>
    </div>

    @if($servicios->isEmpty())
        <div class="guardados-empty">
            Aún no tienes servicios guardados.
            <a href="{{ route('servicio.index') }}" class="btn btn--ghost" style="display:inline-flex;margin-top:12px;font-size:13px;">
                Explorar servicios
            </a>
        </div>
    @else
        <div class="guardados-grid">
            @foreach($servicios as $guardado)
                @php
                    $serv = $guardado->guardable;
                    $srcPortadaServicio = \App\Support\Imagenes::portada($serv->portada_url);
                    $gradients = [
                        'mezcla' => 'linear-gradient(135deg,#111119,#24202c)',
                        'master' => 'linear-gradient(135deg,#16101f,#2a1738)',
                        'otro'   => 'linear-gradient(135deg,#101014,#24242c)',
                    ];
                    $grad = $gradients[$serv->tipo_servicio] ?? 'linear-gradient(135deg,#444,#222)';
                    $tipoLabel = [
                        'mezcla' => 'Mezcla',
                        'master' => 'Mastering',
                        'otro'   => 'Otro',
                    ][$serv->tipo_servicio] ?? ucfirst($serv->tipo_servicio);
                @endphp
                <article class="card card--clickable card--service saved-card"
                         data-card-link="{{ route('servicio.detail', ['id' => $serv->id]) }}"
                         role="link" tabindex="0"
                         aria-label="Ver detalle de {{ $serv->titulo_servicio }}">
                    <div class="card__media">
                        <div class="service-card-media {{ $srcPortadaServicio ? 'has-cover' : 'has-fallback' }}" style="--service-grad: {{ $grad }};">
                            @if($srcPortadaServicio)
                                <img class="service-card-cover"
                                     src="{{ $srcPortadaServicio }}"
                                     alt="Portada {{ $serv->titulo_servicio }}"
                                     loading="lazy"
                                     decoding="async">
                            @else
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                                     stroke="rgba(255,255,255,0.6)" stroke-width="1.5" aria-hidden="true">
                                    <path d="M3 6h18M3 12h18M3 18h18"/>
                                    <circle cx="7" cy="6" r="2" fill="rgba(255,255,255,0.6)" stroke="none"/>
                                    <circle cx="17" cy="12" r="2" fill="rgba(255,255,255,0.6)" stroke="none"/>
                                    <circle cx="10" cy="18" r="2" fill="rgba(255,255,255,0.6)" stroke="none"/>
                                </svg>
                            @endif
                            <span class="service-card-badge">{{ $tipoLabel }}</span>
                        </div>
                    </div>

                    <div class="card__body">
                        <h3 class="card__title">{{ $serv->titulo_servicio }}</h3>
                        @if($serv->usuario)
                            <p class="card__meta">
                                Ingeniero: {{ $serv->usuario->nombre_usuario }}
                            </p>
                        @endif
                        <p class="card__meta">Tipo: {{ $tipoLabel }}</p>
                        @if($serv->plazo_entrega_dias)
                            <p class="card__meta">
                                Plazo: {{ $serv->plazo_entrega_dias }} días
                            </p>
                        @endif

                        <div class="card__foot saved-card__foot">
                            <span class="price saved-card__price">{{ number_format($serv->precio_servicio, 2, ',', '.') }} €</span>
                            <div class="card__actions saved-card__actions">
                                <a class="btn btn--ghost" href="{{ route('servicio.detail', ['id' => $serv->id]) }}">
                                    Ver servicio
                                </a>
                                <form method="POST" action="{{ route('guardados.eliminar', ['tipo' => 'servicio', 'id' => $serv->id]) }}"
                                      style="margin:0;" onsubmit="return confirm('¿Quitar de guardados?')">
                                    @csrf
                                    <button type="submit" class="btn-guardado-quitar" title="Quitar de guardados">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                                        </svg>
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

@endsection
