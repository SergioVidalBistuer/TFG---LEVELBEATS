@extends('layouts.master')

@section('title', 'Dashboard Administrador - LevelBeats')

@php
    $metricas = [
        [
            'label' => 'Usuarios',
            'value' => $totalUsuarios,
            'icon' => 'users',
        ],
        [
            'label' => 'Beats',
            'value' => $totalBeats,
            'icon' => 'music',
        ],
        [
            'label' => 'Ventas',
            'value' => $totalCompras,
            'icon' => 'receipt',
        ],
        [
            'label' => 'Proyectos',
            'value' => $totalProyectos,
            'icon' => 'folder',
        ],
        [
            'label' => 'Servicios',
            'value' => $totalServicios,
            'icon' => 'settings',
        ],
    ];

    $herramientas = [
        [
            'title' => 'Usuarios',
            'desc' => 'Mantenimiento de cuentas',
            'route' => route('usuario.index'),
            'icon' => 'users',
        ],
        [
            'title' => 'Pedidos',
            'desc' => 'Historial de compras',
            'route' => route('compra.index'),
            'icon' => 'receipt',
        ],
        [
            'title' => 'Servicios',
            'desc' => 'Catalogo tecnico',
            'route' => route('admin.servicios.index'),
            'icon' => 'settings',
        ],
        [
            'title' => 'Proyectos',
            'desc' => 'Encargos y trabajos',
            'route' => route('admin.proyectos.index'),
            'icon' => 'folder',
        ],
        [
            'title' => 'Beats',
            'desc' => 'Catalogo musical',
            'route' => route('admin.beats.index'),
            'icon' => 'music',
        ],
        [
            'title' => 'Colecciones',
            'desc' => 'Packs y agrupaciones',
            'route' => route('admin.colecciones.index'),
            'icon' => 'collection',
        ],
    ];
@endphp

@section('content')
<div class="admin-shell">
    <header class="admin-hero">
        <div>
            <span class="admin-kicker">Root</span>
            <h1>Panel de Administracion</h1>
            <p>Vision global de usuarios, ventas, catalogo y actividad operativa de LevelBeats.</p>
        </div>
        <span class="admin-hero__badge">Acceso administrador</span>
    </header>

    <section class="admin-metrics" aria-label="Metricas principales">
        @foreach($metricas as $metrica)
            <article class="admin-metric-card">
                <span class="admin-card-icon" aria-hidden="true">
                    @include('admin.partials.dashboard-icon', ['name' => $metrica['icon']])
                </span>
                <div>
                    <strong>{{ $metrica['value'] }}</strong>
                    <span>{{ $metrica['label'] }}</span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="admin-tools">
        <div class="admin-section-head">
            <div>
                <h2>Herramientas de Gestion</h2>
                <p>Accesos directos a los modulos de supervision y control.</p>
            </div>
        </div>

        <div class="admin-tools-grid">
            @foreach($herramientas as $herramienta)
                <a href="{{ $herramienta['route'] }}" class="admin-tool-card">
                    <span class="admin-card-icon admin-card-icon--tool" aria-hidden="true">
                        @include('admin.partials.dashboard-icon', ['name' => $herramienta['icon']])
                    </span>
                    <span class="admin-tool-card__body">
                        <strong>{{ $herramienta['title'] }}</strong>
                        <small>{{ $herramienta['desc'] }}</small>
                    </span>
                    <svg class="admin-tool-card__arrow" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
