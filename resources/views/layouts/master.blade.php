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
</head>
<body class="d-flex flex-column min-vh-100">

<a class="skip-link" href="#content">Saltar al contenido</a>

<header class="header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3 py-3">

            {{-- LOGO (click -> beat.index) --}}
            <a href="{{ route('beat.index') }}" class="brand__link" style="display: flex; align-items: center;" aria-label="Ir al inicio">
                <img src="{{ asset('media/img/LB-09.png') }}" alt="Logo LevelBeats" style="width: 260px; height: auto; object-fit: contain;">
            </a>

            {{-- Buscador (de momento no hace nada) --}}
            <form class="search flex-grow-1 d-none d-md-flex" action="#" method="GET">
                <input type="text" placeholder="Buscar beats, colecciones..." aria-label="Buscar">
                <button class="btn btn--icon" type="submit" title="Buscar">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm11 3-6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            {{-- NAV PRINCIPAL --}}
            <nav class="nav d-none d-md-flex align-items-center gap-3">
                <a href="{{ route('beat.index') }}">Marketplace</a>
                <a href="{{ route('coleccion.index') }}">Colecciones</a>

                @if(auth()->check())
                    @if(!auth()->user()->esAdmin())
                        <a href="{{ route('carrito.index') }}">🛒 Carrito</a>
                        
                        {{-- AREA PERSONAL (CLIENTE BASE) --}}
                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex; align-items:center; gap: 12px; background: rgba(255, 255, 255, 0.05); padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1);">
                            <span style="color:#fff; font-weight: 700; font-size: 11px; text-transform:uppercase; letter-spacing: 0.5px;">Mi Área</span>
                            <a href="{{ route('usuario.facturacion.index') }}" style="color:rgba(255,255,255,0.8); font-size: 13px;">Compras</a>
                            <a href="{{ route('usuario.encargos.index') }}" style="color:rgba(255,255,255,0.8); font-size: 13px;">Encargos</a>
                        </div>
                    @endif
                    
                    {{-- STUDIO PANEL (Subscripciones Reales / Admin) --}}
                    @php
                        $u = auth()->user();
                        $isAdmin = $u->esAdmin();
                        $isProdActive = $u->tieneSuscripcionActiva('productor');
                        $isIngActive = $u->tieneSuscripcionActiva('ingeniero');
                    @endphp

                    @if($isProdActive || $isIngActive || $isAdmin)
                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex; align-items:center; gap: 12px; background: rgba(0, 230, 118, 0.1); padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(0,230,118,0.2);">
                            <span style="color:#00e676; font-weight: 700; font-size: 11px; text-transform:uppercase; letter-spacing: 0.5px;">Studio</span>
                            @if($isProdActive || $isAdmin)
                                <a href="{{ route('studio.beats.index') }}" style="color:#00e676; font-size: 13px;">Beats</a>
                            @endif
                            @if($isIngActive || $isAdmin)
                                <a href="{{ route('studio.servicios.index') }}" style="color:#00e676; font-size: 13px;">Servicios</a>
                                <a href="{{ route('studio.proyectos.index') }}" style="color:#00e676; font-size: 13px;">Encargos</a>
                            @endif
                        </div>
                    @endif

                    {{-- ROOT ADMIN --}}
                    @if(auth()->user()->esAdmin())
                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex; align-items:center; gap: 12px; background: rgba(255, 82, 82, 0.1); padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(255,82,82,0.2);">
                            <span style="color:#ff5252; font-weight: 700; font-size: 11px; text-transform:uppercase; letter-spacing: 0.5px;">Root</span>
                            <a href="{{ route('admin.dashboard.index') }}" style="color:#ff5252; font-size: 13px; font-weight: bold;">Dashboard Admin</a>
                        </div>
                    @endif
                @endif
            </nav>

            {{-- ACTIONS (Perfil y Sesión) --}}
            <div class="header__actions d-flex align-items-center gap-2">
                @if(auth()->check())
                    <a href="{{ route('usuario.profile') }}" class="muted d-none d-lg-inline header__profile-link" style="text-decoration: none;">Hola, {{ auth()->user()->nombre_usuario }}</a>
                    <a class="btn-lb btn-lb-ghost" href="{{ route('logout') }}">Salir</a>
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

<main id="content" class="container section flex-grow-1">
    @if(session('status'))
        <div class="toast-custom" id="toastStatus">
            <div class="toast-custom__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="toast-custom__text">
                {{ session('status') }}
            </div>
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
                if(toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.style.display = 'none', 300);
                }
            }, 4000);
        </script>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="container footer__bottom">
        Proyecto Laravel 12 - LevelBeats
    </div>
</footer>

</body>
</html>
