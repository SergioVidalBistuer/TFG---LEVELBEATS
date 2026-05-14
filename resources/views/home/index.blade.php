@extends('layouts.master')

@section('title', 'LevelBeats | Inicio')

{{-- La home gestiona su propio contenedor, sin el Bootstrap container --}}
@section('main_class', 'home-main')

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="home-hero">
        <div class="home-hero__main">
            <div class="home-container">
                <div class="home-hero__slide">
                    <h1 class="home-hero__title">
                        Marketplace musical + servicios pro. Encuentra tu sonido, compra licencias y gestiona proyectos.
                    </h1>
                    <div class="home-hero__cta">
                        <a class="home-btn home-btn--primary" href="{{ route('beat.index') }}">Explorar beats</a>
                        <a class="home-btn home-btn--ghost" href="{{ route('coleccion.index') }}">Ver colecciones</a>
                    </div>
                </div>
            </div>

            <div class="home-quickbar-wrap">
                <div class="home-container">
                    <nav class="home-quickbar" aria-label="Accesos rápidos">
                        <a href="{{ route('beat.index') }}">
                            <span class="home-quickbar__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 12h4l2-7 4 14 2-7h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            Beats en tendencia
                        </a>
                        <a href="{{ route('coleccion.index') }}">
                            <span class="home-quickbar__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            Colecciones destacadas
                        </a>
                        <a href="{{ route('carrito.index') }}">
                            <span class="home-quickbar__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M6 6h15l-2 8H8L6 3H3M9 20h.01M18 20h.01" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            Mi carrito
                        </a>
                        <a href="{{ route('usuario.facturacion.index') }}">
                            <span class="home-quickbar__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 12 4.4 7.7M12 12l7.6-4.3M12 12v8.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </span>
                            Mis compras
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== BEATS POPULARES ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-section__head">
                <h2>Beats Populares</h2>
                <a class="home-link" href="{{ route('beat.index') }}">Ver más →</a>
            </div>

            <div class="home-grid home-grid--4">
                @forelse($beatsPopulares as $beat)
                    @php
                        $srcPortadaBeat = \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg');
                    @endphp
                    <article class="home-card">
                        <div class="home-card__media">
                            <img src="{{ $srcPortadaBeat }}"
                                 alt="Portada {{ $beat->titulo_beat }}"
                                 width="640"
                                 height="360"
                                 sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 25vw"
                                 loading="lazy"
                                 decoding="async">
                        </div>
                        <div class="home-card__body">
                            <h3 class="home-card__title">{{ $beat->titulo_beat }}</h3>
                            <p class="home-card__meta">{{ $beat->genero_musical ?? 'Sin género' }} · {{ $beat->usuario->nombre_usuario ?? 'Productor' }}</p>
                            <div class="home-card__foot">
                                <span class="home-price">{{ number_format($beat->precio_base_licencia, 2, ',', '.') }} €</span>
                                <div class="home-card__actions">
                                    <a class="home-btn home-btn--ghost home-btn--sm" href="{{ route('beat.detail', $beat->id) }}">Ver</a>
                                    @auth
                                        @if(!auth()->user()->esAdmin())
                                            <form action="{{ route('carrito.addBeat') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $beat->id }}">
                                                <button class="home-btn home-btn--primary home-btn--sm" type="submit" title="Añadir al carrito">+</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    @for($i = 1; $i <= 4; $i++)
                        <article class="home-card">
                            <div class="home-skeleton" style="height:128px;"></div>
                            <div class="home-card__body">
                                <h3 class="home-card__title">Beat {{ $i }}</h3>
                                <p class="home-card__meta">Próximamente · LevelBeats</p>
                                <div class="home-card__foot"><span class="home-price">—</span></div>
                            </div>
                        </article>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== ROLES / PLANES ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-grid home-grid--2">
                <div class="home-panel home-panel--dark">
                    <h2>Conviértete en Productor, Ingeniero o Artista</h2>
                    <p class="home-muted">
                        Accede a herramientas y planes para vender, contratar servicios profesionales y desarrollar tu música.
                    </p>
                    <div class="home-bullets">
                        <div class="home-bullet">
                            <span class="home-bullet__dot"></span>
                            <div>
                                <h3>Como Productor</h3>
                                <p>Vende beats con licencias y controla tus ventas.</p>
                            </div>
                        </div>
                        <div class="home-bullet">
                            <span class="home-bullet__dot"></span>
                            <div>
                                <h3>Siendo Ingeniero</h3>
                                <p>Recibe encargos de mezcla/master con revisiones.</p>
                            </div>
                        </div>
                        <div class="home-bullet">
                            <span class="home-bullet__dot"></span>
                            <div>
                                <h3>Como Artista</h3>
                                <p>Compra beats, organiza tus licencias y encarga servicios para terminar tus canciones.</p>
                            </div>
                        </div>
                    </div>
                    @auth
                        <a class="home-btn home-btn--primary" href="{{ route('onboarding.roles') }}">Elige tu rol</a>
                    @else
                        <a class="home-btn home-btn--primary" href="{{ route('register') }}">Empezar gratis</a>
                    @endauth
                </div>

                <div class="home-panel home-panel--soft">
                    <div class="home-panel__media">
                        <img src="{{ asset('media/img/imagenesUsoLibreLevelBeats/estudio/estudio3.webp') }}" alt="Estudio musical LevelBeats" width="720" height="480" sizes="(max-width: 980px) 100vw, 50vw" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== GÉNEROS POPULARES ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-section__head">
                <h2>Géneros Populares</h2>
            </div>
            <div class="home-grid home-grid--4">
                @php
    $generos = [
        [
            'nombre' => 'Trap',
            'filtro' => 'Trap',
            'desc' => 'El sonido más demandado',
            'imagen' => 'media/img/imagenesUsoLibreLevelBeats/portadas/portada34Trap.webp'
        ],
        [
            'nombre' => 'Drill',
            'filtro' => 'Drill',
            'desc' => 'Oscuro y contundente',
            'imagen' => 'media/img/imagenesUsoLibreLevelBeats/portadas/portada48Drill.webp'
        ],
        [
            'nombre' => 'Lo-Fi',
            'filtro' => 'Lo-Fi',
            'desc' => 'Chill y relajado',
            'imagen' => 'media/img/imagenesUsoLibreLevelBeats/portadas/portada54Lofi.webp'
        ],
        [
            'nombre' => 'Afrobeats',
            'filtro' => 'Afrobeat',
            'desc' => 'Ritmo y movimiento',
            'imagen' => 'media/img/imagenesUsoLibreLevelBeats/portadas/portada3AfroBeat.webp'
        ],
    ];
@endphp
                @foreach($generos as $genero)
    <a class="home-mini home-mini--link" href="{{ route('beat.index', ['genero' => $genero['filtro']]) }}" aria-label="Ver beats de {{ $genero['nombre'] }}">
        <div class="home-mini__media {{ empty($genero['imagen']) ? 'home-skeleton' : '' }}">
    @if(!empty($genero['imagen']))
        @php
            $srcGenero = \App\Support\Imagenes::portada($genero['imagen']);
        @endphp
        <img src="{{ $srcGenero }}" alt="{{ $genero['nombre'] }}" width="640" height="360" sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 25vw" loading="lazy" decoding="async">
    @endif
</div>

        <div class="home-mini__body">
            <h3>{{ $genero['nombre'] }}</h3>
            <p class="home-muted">{{ $genero['desc'] }}</p>
        </div>
    </a>
@endforeach
            </div>
        </div>
    </section>

    {{-- ===================== BANNER COLECCIÓN ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-collection">
                <div class="home-collection__left">
                    <p class="home-badge home-badge--ghost">LevelBeats</p>
                    <h2>La web con la mayor colección de Beats</h2>
                    <p class="home-muted">Descubre miles de instrumentales, packs y servicios en un solo lugar.</p>
                    <a class="home-btn home-btn--primary" href="{{ route('beat.index') }}">Explora nuestros beats</a>
                </div>
                <div class="home-collection__right">
                    <img src="{{ asset('media/img/imagenesUsoLibreLevelBeats/header/headerConcierto2.webp') }}" alt="Colección de beats LevelBeats" width="720" height="480" sizes="(max-width: 980px) 100vw, 50vw" loading="lazy" decoding="async">
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIOS ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-section__head">
                <h2>Escucha lo que dice nuestra comunidad</h2>
            </div>
            <div class="home-grid home-grid--3">
                <figure class="home-quote">
                    <blockquote>"De las mejores webs para comprar beats."</blockquote>
                    <figcaption>
                        <span class="home-avatar">C</span>
                        <div><strong>Carlos Cescon</strong><span class="home-muted">Artista</span></div>
                    </figcaption>
                </figure>
                <figure class="home-quote">
                    <blockquote>"La recomiendo para todos aquellos que trabajen en esto."</blockquote>
                    <figcaption>
                        <span class="home-avatar">D</span>
                        <div><strong>Doxial</strong><span class="home-muted">Productor</span></div>
                    </figcaption>
                </figure>
                <figure class="home-quote">
                    <blockquote>"Simplemente me parece excelente."</blockquote>
                    <figcaption>
                        <span class="home-avatar">P</span>
                        <div><strong>Penyes</strong><span class="home-muted">Usuario</span></div>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    {{-- ===================== CONTACTO ===================== --}}
    <section class="home-section" id="contacto">
        <div class="home-container">
            <div class="home-contact">
                <h2>Contacta con nosotros</h2>
                @if(session('error'))
                    <div class="home-contact__feedback home-contact__feedback--error">{{ session('error') }}</div>
                @endif
                @if($errors->has('email') || $errors->has('mensaje'))
                    <div class="home-contact__feedback home-contact__feedback--error">
                        {{ $errors->first('email') ?: $errors->first('mensaje') }}
                    </div>
                @endif
                <form class="home-contact__form" method="POST" action="{{ route('contacto.home') }}" novalidate>
                    @csrf
                    <input class="contact-honeypot" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="home-field">
                        <label for="contact_email">Introduce tu correo</label>
                        <input id="contact_email" name="email" type="email" value="{{ old('email') }}" placeholder="tuemail@ejemplo.com" required maxlength="160">
                    </div>
                    <div class="home-field">
                        <label for="contact_msg">Déjanos tu mensaje</label>
                        <input id="contact_msg" name="mensaje" type="text" value="{{ old('mensaje') }}" placeholder="Cuéntanos qué necesitas..." required minlength="10" maxlength="3000">
                    </div>
                    <button class="home-btn home-btn--primary" type="submit">Contactar</button>
                </form>
            </div>
        </div>
    </section>

@endsection
