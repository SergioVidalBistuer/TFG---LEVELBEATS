@extends('layouts.master')

@section('title', 'Dashboard Administrador')

@section('content')
    <div class="container section__head">
        <h2>Panel de Administración Global</h2>
    </div>

    <div class="container">
        <div class="grid grid--4" style="margin-bottom: 32px;">
            <!-- Card Usuarios -->
            <article class="card" style="background: rgba(0,212,255,0.05); border: 1px solid rgba(0,212,255,0.2);">
                <div class="card__body" style="text-align: center; padding: 24px;">
                    <h3 style="color: #00d4ff; font-size: 32px; margin: 0;">{{ $totalUsuarios }}</h3>
                    <p style="color: rgba(255,255,255,0.7); margin-top: 8px;">Usuarios Totales</p>
                </div>
            </article>

            <!-- Card Beats -->
            <article class="card" style="background: rgba(0,230,118,0.05); border: 1px solid rgba(0,230,118,0.2);">
                <div class="card__body" style="text-align: center; padding: 24px;">
                    <h3 style="color: #00e676; font-size: 32px; margin: 0;">{{ $totalBeats }}</h3>
                    <p style="color: rgba(255,255,255,0.7); margin-top: 8px;">Beats en Venta</p>
                </div>
            </article>

            <!-- Card Compras -->
            <article class="card" style="background: rgba(255,193,7,0.05); border: 1px solid rgba(255,193,7,0.2);">
                <div class="card__body" style="text-align: center; padding: 24px;">
                    <h3 style="color: #ffc107; font-size: 32px; margin: 0;">{{ $totalCompras }}</h3>
                    <p style="color: rgba(255,255,255,0.7); margin-top: 8px;">Ventas Completadas</p>
                </div>
            </article>

            <!-- Card Proyectos -->
            <article class="card" style="background: rgba(255,82,82,0.05); border: 1px solid rgba(255,82,82,0.2);">
                <div class="card__body" style="text-align: center; padding: 24px;">
                    <h3 style="color: #ff5252; font-size: 32px; margin: 0;">{{ $totalProyectos }}</h3>
                    <p style="color: rgba(255,255,255,0.7); margin-top: 8px;">Proyectos B2B</p>
                </div>
            </article>
        </div>

        <!-- Menú Rápido -->
        <div
            style="background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); padding: 24px;">
            <h3>Herramientas de Gestión</h3>
            <p style="color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 16px;">Accesos directos a los módulos de
                supervisión.</p>

            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <a href="{{ route('usuario.index') }}" class="btn btn--primary"
                    style="background: transparent; color: #00d4ff; border-color: rgba(0,212,255,0.3);">
                    Usuarios Mantenimiento
                </a>
                <a href="{{ route('compra.index') }}" class="btn btn--primary"
                    style="background: transparent; color: #ffc107; border-color: rgba(255,193,7,0.3);">
                    Pedidos Usuario
                </a>
                <a href="{{ route('admin.auditoria.index') }}" class="btn btn--primary"
                    style="background: transparent; color: #ff5252; border-color: rgba(255,82,82,0.3);">
                    Registro de Auditoría Sistemática
                </a>
            </div>
        </div>
    </div>
@endsection