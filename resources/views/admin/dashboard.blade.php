@extends('layouts.master')

@section('title', 'Dashboard Administrador — LevelBeats')

@section('content')
<style>
    /* ── Admin Dashboard Local Styles ── */
    .admin-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .admin-header__title {
        margin: 0 0 4px;
        font-size: 30px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.5px;
    }
    .admin-header__sub {
        margin: 0;
        color: rgba(255,255,255,0.45);
        font-size: 14px;
    }
    .admin-header__badge {
        background: rgba(169,0,239,0.15);
        border: 1px solid rgba(169,0,239,0.35);
        color: #D26BFF;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    /* Stats Grid */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 32px;
    }
    @media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px)  { .stat-grid { grid-template-columns: repeat(2, 1fr); } }

    .stat-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 22px 18px;
        text-align: center;
        transition: border-color .2s, background .2s;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(80px 60px at 50% 0%, rgba(169,0,239,.12), transparent 70%);
        pointer-events: none;
    }
    .stat-card:hover {
        border-color: rgba(169,0,239,0.3);
        background: rgba(169,0,239,0.05);
    }
    .stat-card__icon {
        font-size: 22px;
        margin-bottom: 10px;
        display: block;
        opacity: .85;
    }
    .stat-card__value {
        font-size: 36px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -1px;
    }
    .stat-card__label {
        font-size: 12px;
        color: rgba(255,255,255,0.45);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
    }

    /* Tools Section */
    .tools-section {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 28px 28px 24px;
    }
    .tools-section__title {
        font-size: 16px;
        font-weight: 700;
        color: rgba(255,255,255,0.9);
        margin: 0 0 4px;
        letter-spacing: -0.2px;
    }
    .tools-section__sub {
        font-size: 13px;
        color: rgba(255,255,255,0.35);
        margin: 0 0 20px;
    }
    .tools-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 900px)  { .tools-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px)  { .tools-grid { grid-template-columns: 1fr; } }

    .tool-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: background .15s, border-color .15s, color .15s, transform .12s;
    }
    .tool-btn:hover {
        background: rgba(169,0,239,0.10);
        border-color: rgba(169,0,239,0.35);
        color: #D26BFF;
        transform: translateY(-1px);
    }
    .tool-btn__icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(169,0,239,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }
    .tool-btn__text { line-height: 1.2; }
    .tool-btn__desc {
        display: block;
        font-size: 11px;
        font-weight: 400;
        color: rgba(255,255,255,0.35);
        margin-top: 2px;
    }
</style>

<div style="max-width: 1160px; margin: 0 auto;">

    {{-- Header --}}
    <div class="admin-header">
        <div>
            <h1 class="admin-header__title">Panel de Administración</h1>
            <p class="admin-header__sub">Visión global de la plataforma LevelBeats</p>
        </div>
        <span class="admin-header__badge">⚡ Root Access</span>
    </div>

    {{-- Stats --}}
    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-card__icon">👥</span>
            <div class="stat-card__value">{{ $totalUsuarios }}</div>
            <div class="stat-card__label">Usuarios</div>
        </div>
        <div class="stat-card">
            <span class="stat-card__icon">🎵</span>
            <div class="stat-card__value">{{ $totalBeats }}</div>
            <div class="stat-card__label">Beats</div>
        </div>
        <div class="stat-card">
            <span class="stat-card__icon">💳</span>
            <div class="stat-card__value">{{ $totalCompras }}</div>
            <div class="stat-card__label">Ventas</div>
        </div>
        <div class="stat-card">
            <span class="stat-card__icon">📁</span>
            <div class="stat-card__value">{{ $totalProyectos }}</div>
            <div class="stat-card__label">Proyectos B2B</div>
        </div>
        <div class="stat-card">
            <span class="stat-card__icon">🛠️</span>
            <div class="stat-card__value">{{ $totalServicios }}</div>
            <div class="stat-card__label">Servicios</div>
        </div>
    </div>

    {{-- Tools --}}
    <div class="tools-section">
        <p class="tools-section__title">Herramientas de Gestión</p>
        <p class="tools-section__sub">Accesos directos a los módulos de supervisión y control</p>

        <div class="tools-grid">
            <a href="{{ route('usuario.index') }}" class="tool-btn">
                <span class="tool-btn__icon">👥</span>
                <span class="tool-btn__text">
                    Usuarios
                    <span class="tool-btn__desc">Mantenimiento de cuentas</span>
                </span>
            </a>
            <a href="{{ route('compra.index') }}" class="tool-btn">
                <span class="tool-btn__icon">🧾</span>
                <span class="tool-btn__text">
                    Pedidos
                    <span class="tool-btn__desc">Historial de compras</span>
                </span>
            </a>
            <a href="{{ route('admin.servicios.index') }}" class="tool-btn">
                <span class="tool-btn__icon">🛠️</span>
                <span class="tool-btn__text">
                    Servicios B2B
                    <span class="tool-btn__desc">Catálogo técnico global</span>
                </span>
            </a>
            <a href="{{ route('admin.auditoria.index') }}" class="tool-btn">
                <span class="tool-btn__icon">🔍</span>
                <span class="tool-btn__text">
                    Auditoría
                    <span class="tool-btn__desc">Registro de acciones</span>
                </span>
            </a>
        </div>
    </div>

</div>
@endsection