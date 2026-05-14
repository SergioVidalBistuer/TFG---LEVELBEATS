<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LevelBeats')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tu CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* ===== DROPDOWNS DEL HEADER ===== */
        .user-dropdown { position: relative; }

        .user-dropdown__trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.10);
            background: rgba(255,255,255,0.035);
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            font-weight: 750;
            text-decoration: none;
            transition: background .15s, border-color .15s;
            user-select: none;
            font-family: var(--font-brand);
        }
        .user-dropdown__trigger:hover,
        .user-dropdown__trigger.is-open {
            background: rgba(169,0,239,0.09);
            border-color: rgba(210,107,255,0.34);
            color: #fff;
        }
        .user-dropdown__trigger svg.chevron {
            transition: transform .2s ease;
            flex-shrink: 0;
        }
        .user-dropdown__trigger.is-open svg.chevron { transform: rotate(180deg); }

        .user-dropdown__menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 190px;
            background: rgba(12,12,18,.98);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.55);
            overflow: hidden;
            z-index: 200;
        }
        .user-dropdown__menu.is-open {
            display: block;
            animation: dropFadeIn .15s ease;
        }
        @keyframes dropFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .user-dropdown__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            color: rgba(255,255,255,0.88);
            font-size: 13px;
            text-decoration: none;
            transition: background .12s, color .12s;
            font-family: var(--font-body);
        }
        .user-dropdown__item:hover { background: rgba(169,0,239,0.08); color: #fff; }
        .user-dropdown__item.is-active {
            background: rgba(169,0,239,0.12);
            color: #fff;
        }
        .user-dropdown__item svg { flex-shrink: 0; opacity: .75; }
        .user-dropdown__item:hover svg { opacity: 1; }

        .user-dropdown__divider {
            height: 1px;
            background: rgba(255,255,255,0.07);
            margin: 4px 0;
        }
        .user-dropdown__item--danger { color: rgba(255,100,100,0.9); }
        .user-dropdown__item--danger:hover { background: rgba(255,60,60,0.08); color: #ff6b6b; }

        .product-actions-dropdown .user-dropdown__trigger {
            padding: 6px 10px;
        }

        .product-actions-dropdown .user-dropdown__menu {
            top: auto;
            bottom: calc(100% + 8px);
            min-width: 150px;
            z-index: 260;
        }

        .user-dropdown__avatar {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: rgba(169,0,239,0.16);
            border: 1px solid rgba(255,255,255,0.10);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }

        .user-dropdown__avatar img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 14px 0;
        }

        .header .brand__link img {
            width: 220px;
            height: auto;
            object-fit: contain;
        }

        .nav {
            gap: 10px;
            white-space: nowrap;
        }

        .nav .user-dropdown__menu .user-dropdown__item,
        .nav .user-dropdown__menu .user-dropdown__item:hover,
        .nav .user-dropdown__menu .user-dropdown__item.is-active {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            border: 0;
            border-radius: 0;
            font-size: 14px;
            font-weight: 400;
            text-decoration: none;
            box-shadow: none;
        }

        .nav .user-dropdown__menu .user-dropdown__item {
            background: transparent;
            color: rgba(255,255,255,0.88);
        }

        .nav .user-dropdown__menu .user-dropdown__item:hover {
            background: rgba(169,0,239,0.10);
            color: #fff;
        }

        .nav .user-dropdown__menu .user-dropdown__item.is-active {
            background: rgba(169,0,239,0.16);
            color: #fff;
        }

        .nav-divider {
            color: rgba(255,255,255,0.18);
            margin: 0 2px;
        }

        .project-status-select {
            width: 100%;
            padding: 12px;
            background: #161620;
            color: #fff;
            border: 1px solid rgba(169,0,239,0.45);
            border-radius: 8px;
            outline: none;
            color-scheme: dark;
        }

        .project-status-select:focus {
            border-color: rgba(210,107,255,0.85);
            box-shadow: 0 0 0 3px rgba(169,0,239,0.18);
        }

        .project-status-select option {
            background: #12121a;
            color: #fff;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .status-badge--process {
            background: rgba(169,0,239,0.14);
            color: #D26BFF;
        }

        .status-badge--done {
            background: rgba(210,107,255,0.16);
            color: #f0c7ff;
        }

        .shared-files-box {
            background: rgba(8,8,12,.96);
            padding: 24px;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            margin-bottom: 24px;
        }

        .download-link {
            font-size: 12px;
            padding: 4px 10px;
            color: #D26BFF !important;
            border-color: rgba(210,107,255,0.35) !important;
        }

        .chat-bubble--mine {
            background: rgba(169,0,239,0.14);
            color: #f2d7ff;
            border: 1px solid rgba(210,107,255,0.28);
        }

        .chat-bubble--other {
            background: rgba(255,255,255,0.05);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<a class="skip-link" href="#content">Saltar al contenido</a>

<header class="header">
    <div class="container">
        <div class="header__row">

            {{-- LOGO --}}
            <a href="{{ route('home.index') }}" class="brand__link" style="display:flex;align-items:center;" aria-label="Ir al inicio">
                <img src="{{ asset('media/img/LB-09.png') }}" alt="Logo LevelBeats">
            </a>

            {{-- Buscador --}}
            <form class="search flex-grow-1 d-none d-md-flex" action="{{ route('search.index') }}" method="GET" autocomplete="off">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar beats, colecciones..." aria-label="Buscar" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                <button class="btn btn--icon" type="submit" title="Buscar">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm11 3-6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            {{-- NAV PRINCIPAL --}}
            <nav class="nav d-none d-md-flex align-items-center">
                <div class="user-dropdown" data-dropdown>
                    <button
                        class="user-dropdown__trigger"
                        id="marketplaceDropdownTrigger"
                        type="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="marketplaceDropdownMenu"
                    >
                        <span>Marketplace</span>
                        <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="user-dropdown__menu" id="marketplaceDropdownMenu" role="menu" aria-labelledby="marketplaceDropdownTrigger">
                        <a class="user-dropdown__item {{ request()->routeIs('beat.*') ? 'is-active' : '' }}" href="{{ route('beat.index') }}" role="menuitem">Beats</a>
                        <a class="user-dropdown__item {{ request()->routeIs('coleccion.*') ? 'is-active' : '' }}" href="{{ route('coleccion.index') }}" role="menuitem">Colecciones</a>
                        <a class="user-dropdown__item {{ request()->routeIs('perfiles.*') ? 'is-active' : '' }}" href="{{ route('perfiles.index') }}" role="menuitem">Perfiles</a>
                    </div>
                </div>

                <a href="{{ route('servicio.index') }}">Servicios</a>
                <a class="{{ request()->routeIs('contacto.*') ? 'is-active' : '' }}" href="{{ route('contacto.index') }}">Contacto</a>

                @if(auth()->check())
                    @if(!auth()->user()->esAdmin())
                        <a href="{{ route('carrito.index') }}">Carrito</a>

                        <span class="nav-divider">|</span>
                        <div class="user-dropdown" data-dropdown>
                            <button
                                class="user-dropdown__trigger"
                                id="areaDropdownTrigger"
                                type="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="areaDropdownMenu"
                            >
                                <span>Mi Área</span>
                                <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>

                            <div class="user-dropdown__menu" id="areaDropdownMenu" role="menu" aria-labelledby="areaDropdownTrigger">
                                <a class="user-dropdown__item {{ request()->routeIs('usuario.facturacion.*') || request()->routeIs('compra.*') ? 'is-active' : '' }}" href="{{ route('usuario.facturacion.index') }}" role="menuitem">Compras</a>
                                <a class="user-dropdown__item {{ request()->routeIs('usuario.encargos.*') ? 'is-active' : '' }}" href="{{ route('usuario.encargos.index') }}" role="menuitem">Encargos</a>
                            </div>
                        </div>
                    @endif

                    @php
                        $u = auth()->user();
                        $isAdmin = $u->esAdmin();
                        $isProdActive = $u->tieneSuscripcionActiva('productor');
                        $isIngActive = $u->tieneSuscripcionActiva('ingeniero');
                        $hasProfessionalRole = $u->tieneRol('productor') || $u->tieneRol('ingeniero');
                    @endphp

                    @if(!$isAdmin && ($isProdActive || $isIngActive || $hasProfessionalRole))
                        <span class="nav-divider">|</span>
                        <div class="user-dropdown" data-dropdown>
                            <button
                                class="user-dropdown__trigger"
                                id="studioDropdownTrigger"
                                type="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="studioDropdownMenu"
                            >
                                <span>Studio</span>
                                <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>

                            <div class="user-dropdown__menu" id="studioDropdownMenu" role="menu" aria-labelledby="studioDropdownTrigger">
                                @if($isProdActive)
                                    <a class="user-dropdown__item {{ request()->routeIs('studio.beats.*') ? 'is-active' : '' }}" href="{{ route('studio.beats.index') }}" role="menuitem">Beats</a>
                                    <a class="user-dropdown__item {{ request()->routeIs('studio.colecciones.*') ? 'is-active' : '' }}" href="{{ route('studio.colecciones.index') }}" role="menuitem">Colecciones</a>
                                @endif
                                @if($isIngActive)
                                    <a class="user-dropdown__item {{ request()->routeIs('studio.servicios.*') ? 'is-active' : '' }}" href="{{ route('studio.servicios.index') }}" role="menuitem">Servicios</a>
                                    <a class="user-dropdown__item {{ request()->routeIs('studio.proyectos.*') ? 'is-active' : '' }}" href="{{ route('studio.proyectos.index') }}" role="menuitem">Encargos</a>
                                @endif
                                @if($hasProfessionalRole)
                                    <a class="user-dropdown__item {{ request()->routeIs('analiticas.*') ? 'is-active' : '' }}" href="{{ route('analiticas.index') }}" role="menuitem">Analíticas</a>
                                @endif
                            </div>
                        </div>
                    @endif


                    @if(auth()->user()->esAdmin())
                        <span class="nav-divider">|</span>
                        <a class="admin-nav-pill" href="{{ route('admin.dashboard.index') }}">
                            <span>Root</span>
                            <strong>Dashboard Admin</strong>
                        </a>
                    @endif
                @endif
            </nav>

            {{-- ACTIONS --}}
            <div class="header__actions d-flex align-items-center gap-2">
                @if(auth()->check())
                    @php
                        $mensajesNoLeidos = 0;

                        if (\Illuminate\Support\Facades\Schema::hasTable('conversacion') && \Illuminate\Support\Facades\Schema::hasTable('mensaje_directo')) {
                            $mensajesNoLeidos = \App\Models\MensajeDirecto::query()
                                ->where('emisor_id', '<>', auth()->id())
                                ->where('leido', false)
                                ->whereHas('conversacion', function ($query) {
                                    $query->where('usuario_uno_id', auth()->id())
                                        ->orWhere('usuario_dos_id', auth()->id());
                                })
                                ->count();
                        }

                        $mensajesNoLeidosLabel = $mensajesNoLeidos >= 100 ? '+99' : $mensajesNoLeidos;
                        $mostrarAnaliticasPerfil = auth()->user()->esAdmin()
                            || (!auth()->user()->tieneRol('productor') && !auth()->user()->tieneRol('ingeniero'));
                    @endphp

                    <div class="user-dropdown" id="userDropdown" data-dropdown>
                        <button
                            class="user-dropdown__trigger"
                            id="userDropdownTrigger"
                            type="button"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-controls="userDropdownMenu"
                        >
                            <span class="user-dropdown__avatar">
                                @if(auth()->user()->url_foto_perfil)
                                    <img src="{{ asset(auth()->user()->url_foto_perfil) }}" alt="{{ auth()->user()->nombre_usuario }}">
                                @else
                                    {{ strtoupper(substr(auth()->user()->nombre_usuario, 0, 1)) }}
                                @endif
                            </span>
                            <span class="d-none d-lg-inline">Hola, {{ auth()->user()->nombre_usuario }}</span>
                            <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="user-dropdown__menu" id="userDropdownMenu" role="menu" aria-labelledby="userDropdownTrigger">
                            <a class="user-dropdown__item" href="{{ route('usuario.profile') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                Ver perfil
                            </a>
                            <a class="user-dropdown__item" href="{{ route('usuario.productos.index') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/>
                                </svg>
                                Mis productos
                            </a>
                            <a class="user-dropdown__item {{ request()->routeIs('usuario.guardados.*') ? 'is-active' : '' }}" href="{{ route('usuario.guardados.index') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                </svg>
                                Guardados
                            </a>
                            <a class="user-dropdown__item {{ request()->routeIs('mensajes.*') ? 'is-active' : '' }}" href="{{ route('mensajes.index') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
                                </svg>
                                <span class="user-dropdown__item-label">Mensajes</span>
                                @if($mensajesNoLeidos > 0)
                                    <span class="user-dropdown__badge">{{ $mensajesNoLeidosLabel }}</span>
                                @endif
                            </a>
                            @if($mostrarAnaliticasPerfil)
                                <a class="user-dropdown__item {{ request()->routeIs('analiticas.*') ? 'is-active' : '' }}" href="{{ route('analiticas.index') }}" role="menuitem">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                        <path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-3"/>
                                    </svg>
                                    Analíticas
                                </a>
                            @endif
                            <a class="user-dropdown__item {{ request()->routeIs('usuario.settings*') ? 'is-active' : '' }}" href="{{ route('usuario.settings') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.04.04a2 2 0 1 1-2.83 2.83l-.04-.04A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.08V21a2 2 0 1 1-4 0v-.06A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.04.04a2 2 0 1 1-2.83-2.83l.04-.04A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.08-.4H3a2 2 0 1 1 0-4h.06A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.04-.04a2 2 0 1 1 2.83-2.83l.04.04A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6A1.7 1.7 0 0 0 10.4 2.92V3a2 2 0 1 1 4 0v.06A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.04-.04a2 2 0 1 1 2.83 2.83l-.04.04A1.7 1.7 0 0 0 19.4 9c.38.16.73.37 1 .6.32.27.6.68.6 1.08V11a2 2 0 1 1 0 4h-.06a1.7 1.7 0 0 0-1.54 1Z"/>
                                </svg>
                                Ajustes de la cuenta
                            </a>
                            <a class="user-dropdown__item {{ request()->routeIs('usuario.plan.*') ? 'is-active' : '' }}" href="{{ route('usuario.plan.index') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                                </svg>
                                Cambiar plan
                            </a>
                            <div class="user-dropdown__divider"></div>
                            <a class="user-dropdown__item user-dropdown__item--danger" href="{{ route('logout') }}" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Cerrar sesión
                            </a>
                        </div>
                    </div>

                @else
                    <a class="btn-lb btn-lb-login" href="{{ route('login') }}">Acceder</a>
                @endif
            </div>

        </div>
    </div>
</header>

{{-- HERO opcional por sección --}}
@hasSection('hero')
    @yield('hero')
@endif

<main id="content" class="@yield('main_class', 'container section') flex-grow-1">
    @if(session('status'))
        <div class="toast-custom" id="toastStatus">
            <div class="toast-custom__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="toast-custom__text">{{ session('status') }}</div>
            <button type="button" class="toast-custom__close" onclick="document.getElementById('toastStatus').style.display='none';" aria-label="Cerrar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toastStatus');
                if(toast) { toast.style.opacity = '0'; setTimeout(() => toast.style.display = 'none', 300); }
            }, 4000);
        </script>
    @endif

    @yield('content')
</main>

<footer class="footer footer-lb">
    <div class="container footer-lb__grid">
        <div class="footer-lb__brand">
            <a class="footer-lb__logo-link" href="{{ route('home.index') }}" aria-label="Ir al inicio">
                <img src="{{ asset('media/img/LB-09-hero.png') }}" alt="LevelBeats">
            </a>
            <p>Trabajo Fin de Grado Adrián Campos y Sergio Vidal - LevelBeats</p>
        </div>

        <nav class="footer-lb__nav" aria-label="Navegación del pie">
            <h2>Navegación</h2>
            <div class="footer-lb__links">
                <a href="{{ route('home.index') }}">Inicio</a>
                <a href="{{ route('beat.index') }}">Beats</a>
                <a href="{{ route('coleccion.index') }}">Colecciones</a>
                <a href="{{ route('servicio.index') }}">Servicios</a>
                <a href="{{ route('perfiles.index') }}">Perfiles</a>
                <a href="{{ route('contacto.index') }}">Contacto</a>
            </div>
        </nav>

        <nav class="footer-lb__nav" aria-label="Área de usuario">
            <h2>Mi Área</h2>
            <div class="footer-lb__links">
            @auth
                <a href="{{ route('usuario.profile') }}">Ver perfil</a>
                <a href="{{ route('usuario.productos.index') }}">Mis productos</a>
                <a href="{{ route('usuario.facturacion.index') }}">Compras</a>
                <a href="{{ route('mensajes.index') }}">Mensajes</a>
            @else
                <a href="{{ route('login') }}">Acceder</a>
                <a href="{{ route('register') }}">Crear cuenta</a>
                <a href="{{ route('carrito.index') }}">Carrito</a>
            @endauth
            </div>
        </nav>

        <nav class="footer-lb__nav" aria-label="Páginas legales">
            <h2>Legal</h2>
            <div class="footer-lb__links footer-lb__links--single">
                <a href="{{ route('legal.aviso') }}">Aviso Legal</a>
                <a href="{{ route('legal.privacidad') }}">Política de Privacidad</a>
            </div>
        </nav>
    </div>
</footer>

{{-- Dropdown JS --}}
<script>
(function() {
    const dropdowns = document.querySelectorAll('[data-dropdown]');
    if (!dropdowns.length) return;

    function closeDropdown(dropdown) {
        const trigger = dropdown.querySelector('.user-dropdown__trigger');
        const menu = dropdown.querySelector('.user-dropdown__menu');
        if (!trigger || !menu) return;

        menu.classList.remove('is-open');
        trigger.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function openDropdown(dropdown) {
        const trigger = dropdown.querySelector('.user-dropdown__trigger');
        const menu = dropdown.querySelector('.user-dropdown__menu');
        if (!trigger || !menu) return;

        dropdowns.forEach(function(other) {
            if (other !== dropdown) closeDropdown(other);
        });

        menu.classList.add('is-open');
        trigger.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    dropdowns.forEach(function(dropdown) {
        const trigger = dropdown.querySelector('.user-dropdown__trigger');
        const menu = dropdown.querySelector('.user-dropdown__menu');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.contains('is-open') ? closeDropdown(dropdown) : openDropdown(dropdown);
        });
    });

    document.addEventListener('click', function(e) {
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(e.target)) closeDropdown(dropdown);
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdowns.forEach(closeDropdown);
        }
    });
})();
</script>

{{-- Cards clicables --}}
<script>
(function() {
    const interactiveSelector = [
        'a',
        'button',
        'form',
        'input',
        'select',
        'textarea',
        '.btn',
        '.user-dropdown',
        '.user-dropdown__menu',
        '.user-dropdown__trigger'
    ].join(',');

    function isInteractiveClick(target, card) {
        const interactive = target.closest(interactiveSelector);
        return interactive && card.contains(interactive);
    }

    document.addEventListener('click', function(e) {
        const card = e.target.closest('[data-card-link]');
        if (!card || isInteractiveClick(e.target, card)) return;

        const href = card.getAttribute('data-card-link');
        if (href) window.location.href = href;
    });

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;

        const card = e.target.closest('[data-card-link]');
        if (!card || isInteractiveClick(e.target, card)) return;

        const href = card.getAttribute('data-card-link');
        if (!href) return;

        e.preventDefault();
        window.location.href = href;
    });
})();
</script>

{{-- Dropdowns custom de filtros --}}
<script>
(function() {
    const dropdownSelector = '.catalog-filter-dropdown';
    const itemSelector = '.catalog-filter-dropdown__item';

    function getItemText(item) {
        return Array.from(item.childNodes)
            .filter(function(node) {
                return node.nodeType === Node.TEXT_NODE;
            })
            .map(function(node) {
                return node.textContent.trim();
            })
            .filter(Boolean)
            .join(' ');
    }

    function setActiveItem(dropdown, item) {
        const input = item.querySelector('input[type="radio"]');
        const triggerText = dropdown.querySelector('summary > span:first-child');
        if (!input || !triggerText) return;

        input.checked = true;
        dropdown.querySelectorAll(itemSelector).forEach(function(option) {
            option.classList.toggle('is-active', option === item);
        });

        triggerText.textContent = getItemText(item);
        dropdown.removeAttribute('open');
    }

    document.addEventListener('click', function(e) {
        const item = e.target.closest(itemSelector);
        if (item) {
            const dropdown = item.closest(dropdownSelector);
            if (!dropdown) return;

            e.preventDefault();
            e.stopPropagation();
            setActiveItem(dropdown, item);
            return;
        }

        document.querySelectorAll(dropdownSelector + '[open]').forEach(function(dropdown) {
            if (!dropdown.contains(e.target)) {
                dropdown.removeAttribute('open');
            }
        });
    });

    document.addEventListener('toggle', function(e) {
        const dropdown = e.target;
        if (!dropdown.matches || !dropdown.matches(dropdownSelector) || !dropdown.open) return;

        document.querySelectorAll(dropdownSelector + '[open]').forEach(function(other) {
            if (other !== dropdown) {
                other.removeAttribute('open');
            }
        });
    }, true);
})();
</script>

</body>
</html>
