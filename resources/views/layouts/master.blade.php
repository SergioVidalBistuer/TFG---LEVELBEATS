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

            {{-- NAV --}}
            <nav class="nav d-none d-md-flex">
                <a href="{{ route('beat.index') }}">Beats</a>
                <a href="{{ route('coleccion.index') }}">Colecciones</a>

                @if(session('rol') === 'admin')
                    <a href="{{ route('usuario.index') }}">Usuarios</a>
                @endif

                @if(session()->has('usuario_id'))
                    <a href="{{ route('carrito.index') }}">Carrito</a>
                    <a href="{{ route('compra.index') }}">Mis Pedidos</a>
                @endif
            </nav>

            {{-- ACTIONS (fuera del nav) --}}
            <div class="header__actions d-flex align-items-center gap-2">
                @if(session()->has('usuario_id'))
                    <a href="{{ route('usuario.profile') }}" class="muted d-none d-lg-inline header__profile-link" style="text-decoration: none;">Hola, {{ session('usuario_nombre') }}</a>
                    <a class="btn-lb btn-lb-ghost" href="{{ route('logout') }}">Logout</a>
                @else
                    <a class="btn-lb btn-lb-login" href="{{ route('login') }}">Login</a>
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
