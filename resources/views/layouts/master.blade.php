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
        /* ===== DROPDOWN DE USUARIO ===== */
        .user-dropdown { position: relative; }

        .user-dropdown__trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s, border-color .15s;
            user-select: none;
            font-family: inherit;
        }
        .user-dropdown__trigger:hover,
        .user-dropdown__trigger.is-open {
            background: rgba(169,0,239,0.12);
            border-color: rgba(169,0,239,0.45);
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
            background: #12121a;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
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
            font-size: 14px;
            text-decoration: none;
            transition: background .12s, color .12s;
        }
        .user-dropdown__item:hover { background: rgba(169,0,239,0.10); color: #fff; }
        .user-dropdown__item svg { flex-shrink: 0; opacity: .75; }
        .user-dropdown__item:hover svg { opacity: 1; }

        .user-dropdown__divider {
            height: 1px;
            background: rgba(255,255,255,0.07);
            margin: 4px 0;
        }
        .user-dropdown__item--danger { color: rgba(255,100,100,0.9); }
        .user-dropdown__item--danger:hover { background: rgba(255,60,60,0.08); color: #ff6b6b; }

        .user-dropdown__avatar {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #A900EF, #D26BFF);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #0b0b0f;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<a class="skip-link" href="#content">Saltar al contenido</a>

<header class="header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3 py-3">

            {{-- LOGO --}}
            <a href="{{ route('home.index') }}" class="brand__link" style="display:flex;align-items:center;" aria-label="Ir al inicio">
                <img src="{{ asset('media/img/LB-09.png') }}" alt="Logo LevelBeats" style="width:260px;height:auto;object-fit:contain;">
            </a>

            {{-- Buscador --}}
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
                <a href="{{ route('beat.index') }}">Beats</a>
                <a href="{{ route('coleccion.index') }}">Colecciones</a>

                @if(auth()->check())
                    @if(!auth()->user()->esAdmin())
                        <a href="{{ route('carrito.index') }}">🛒 Carrito</a>

                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.05);padding:4px 12px;border-radius:4px;border:1px solid rgba(255,255,255,.1);">
                            <span style="color:#fff;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Mi Área</span>
                            <a href="{{ route('usuario.facturacion.index') }}" style="color:rgba(255,255,255,.8);font-size:13px;">Compras</a>
                            <a href="{{ route('usuario.encargos.index') }}" style="color:rgba(255,255,255,.8);font-size:13px;">Encargos</a>
                        </div>
                    @endif

                    @php
                        $u = auth()->user();
                        $isAdmin = $u->esAdmin();
                        $isProdActive = $u->tieneSuscripcionActiva('productor');
                        $isIngActive = $u->tieneSuscripcionActiva('ingeniero');
                    @endphp

                    @if($isProdActive || $isIngActive || $isAdmin)
                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex;align-items:center;gap:12px;background:rgba(0,230,118,.1);padding:4px 12px;border-radius:4px;border:1px solid rgba(0,230,118,.2);">
                            <span style="color:#00e676;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Studio</span>
                            @if($isProdActive || $isAdmin)
                                <a href="{{ route('studio.beats.index') }}" style="color:#00e676;font-size:13px;">Beats</a>
                            @endif
                            @if($isIngActive)
                                <a href="{{ route('studio.servicios.index') }}" style="color:#00e676;font-size:13px;">Servicios</a>
                                <a href="{{ route('studio.proyectos.index') }}" style="color:#00e676;font-size:13px;">Encargos</a>
                            @endif
                        </div>
                    @endif


                    @if(auth()->user()->esAdmin())
                        <span style="color:rgba(255,255,255,0.2);">|</span>
                        <div style="display:flex;align-items:center;gap:12px;background:rgba(255,82,82,.1);padding:4px 12px;border-radius:4px;border:1px solid rgba(255,82,82,.2);">
                            <span style="color:#ff5252;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Root</span>
                            <a href="{{ route('admin.dashboard.index') }}" style="color:#ff5252;font-size:13px;font-weight:bold;">Dashboard Admin</a>
                        </div>
                    @endif
                @endif
            </nav>

            {{-- ACTIONS --}}
            <div class="header__actions d-flex align-items-center gap-2">
                @if(auth()->check())
                    <div class="user-dropdown" id="userDropdown">
                        <button
                            class="user-dropdown__trigger"
                            id="userDropdownTrigger"
                            type="button"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-controls="userDropdownMenu"
                        >
                            <span class="user-dropdown__avatar">
                                {{ strtoupper(substr(auth()->user()->nombre_usuario, 0, 1)) }}
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
                            <a class="user-dropdown__item" href="{{ route('onboarding.planes', ['rol' => 'productor']) }}" role="menuitem">
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

<footer class="footer">
    <div class="container footer__bottom">
        Proyecto Laravel 12 - LevelBeats
    </div>
</footer>

{{-- Dropdown JS --}}
<script>
(function() {
    const trigger = document.getElementById('userDropdownTrigger');
    const menu    = document.getElementById('userDropdownMenu');
    if (!trigger || !menu) return;

    function openMenu()  { menu.classList.add('is-open');    trigger.classList.add('is-open');    trigger.setAttribute('aria-expanded','true'); }
    function closeMenu() { menu.classList.remove('is-open'); trigger.classList.remove('is-open'); trigger.setAttribute('aria-expanded','false'); }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.contains('is-open') ? closeMenu() : openMenu();
    });
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('userDropdown');
        if (wrap && !wrap.contains(e.target)) closeMenu();
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeMenu(); });
})();
</script>

</body>
</html>
