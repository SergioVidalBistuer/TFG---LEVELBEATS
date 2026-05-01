@extends('layouts.master')

@section('title', 'LevelBeat | Inicio')

{{-- La home gestiona su propio contenedor, sin el Bootstrap container --}}
@section('main_class', 'home-main')

@section('content')

    {{-- ===================== HERO ===================== --}}
    <section class="home-hero">
        <div class="home-container home-hero__grid">

            <div class="home-hero__slider">
                <div class="home-hero__slide">
                    <p class="home-badge">Nuevo</p>
                    <h1>LevelBeat</h1>
                    <p class="home-hero__subtitle">
                        Marketplace musical + servicios pro. Encuentra tu sonido, compra licencias y gestiona proyectos.
                    </p>
                    <div class="home-hero__cta">
                        <a class="home-btn home-btn--primary" href="{{ route('beat.index') }}">Explorar beats</a>
                        <a class="home-btn home-btn--ghost" href="{{ route('coleccion.index') }}">Ver colecciones</a>
                    </div>
                </div>
                <div class="home-hero__controls" aria-hidden="true">
                    <div class="home-dots">
                        <span class="home-dot home-dot--active"></span>
                        <span class="home-dot"></span>
                        <span class="home-dot"></span>
                    </div>
                </div>
            </div>

            <aside class="home-hero__side">
                <div class="home-sidecard">
                    <h2>Acceso rápido</h2>
                    <ul class="home-sidecard__list">
                        <li><a href="{{ route('beat.index') }}">🔥 Beats en tendencia</a></li>
                        <li><a href="{{ route('coleccion.index') }}">🎵 Colecciones destacadas</a></li>
                        @auth
                            <li><a href="{{ route('carrito.index') }}">🛒 Mi carrito</a></li>
                            <li><a href="{{ route('usuario.facturacion.index') }}">📦 Mis compras</a></li>
                        @else
                            <li><a href="{{ route('register') }}">🚀 Registrarse gratis</a></li>
                            <li><a href="{{ route('login') }}">🔑 Iniciar sesión</a></li>
                        @endauth
                    </ul>
                </div>
            </aside>

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
                    <article class="home-card">
                        <div class="home-card__media">
                            <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                                 alt="Portada {{ $beat->titulo_beat }}">
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
                                <p class="home-card__meta">Próximamente · LevelBeat</p>
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
                    <h2>Conviértete en Productor, Ingeniero o Diseñador</h2>
                    <p class="home-muted">
                        Accede a herramientas y planes para vender, gestionar clientes y trabajar por proyectos.
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
                                <h3>Eligiendo ser Diseñador</h3>
                                <p>Publica portadas o gestiona encargos personalizados.</p>
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
                    <div class="home-panel__media home-skeleton"></div>
                    <p class="home-panel__caption">Productores, Ingenieros y Diseñadores en LevelBeat</p>
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
                        ['nombre' => 'Trap',      'desc' => 'El sonido más demandado'],
                        ['nombre' => 'Drill',     'desc' => 'Oscuro y contundente'],
                        ['nombre' => 'Lo-Fi',     'desc' => 'Chill y relajado'],
                        ['nombre' => 'Afrobeats', 'desc' => 'Ritmo y movimiento'],
                    ];
                @endphp
                @foreach($generos as $genero)
                    <article class="home-mini">
                        <div class="home-mini__media home-skeleton"></div>
                        <div class="home-mini__body">
                            <h3>{{ $genero['nombre'] }}</h3>
                            <p class="home-muted">{{ $genero['desc'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== BANNER COLECCIÓN ===================== --}}
    <section class="home-section">
        <div class="home-container">
            <div class="home-collection">
                <div class="home-collection__left">
                    <p class="home-badge home-badge--ghost">LevelBeat</p>
                    <h2>La web con la mayor colección de Beats</h2>
                    <p class="home-muted">Descubre miles de instrumentales, packs y servicios en un solo lugar.</p>
                    <a class="home-btn home-btn--primary" href="{{ route('beat.index') }}">Explora nuestros beats</a>
                </div>
                <div class="home-collection__right home-skeleton"></div>
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
                        <span class="home-avatar"></span>
                        <div><strong>Carlos Cescon</strong><span class="home-muted">Artista</span></div>
                    </figcaption>
                </figure>
                <figure class="home-quote">
                    <blockquote>"La recomiendo para todos aquellos que trabajen en esto."</blockquote>
                    <figcaption>
                        <span class="home-avatar"></span>
                        <div><strong>Doxial</strong><span class="home-muted">Productor</span></div>
                    </figcaption>
                </figure>
                <figure class="home-quote">
                    <blockquote>"Simplemente me parece excelente."</blockquote>
                    <figcaption>
                        <span class="home-avatar"></span>
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
                <form class="home-contact__form">
                    <div class="home-field">
                        <label for="contact_email">Introduce tu correo</label>
                        <input id="contact_email" name="email" type="email" placeholder="tuemail@ejemplo.com" required>
                    </div>
                    <div class="home-field">
                        <label for="contact_msg">Déjanos tu mensaje</label>
                        <input id="contact_msg" name="msg" type="text" placeholder="Cuéntanos qué necesitas..." required>
                    </div>
                    <button class="home-btn home-btn--primary" type="submit">Contactar</button>
                </form>
            </div>
        </div>
    </section>

@endsection
