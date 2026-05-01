@extends('layouts.master')

@section('title', 'Admin — Gestión de Servicios B2B')

@section('content')
<style>
    .admin-page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .admin-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: rgba(255,255,255,0.4);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 12px;
        transition: color .15s;
    }
    .admin-back:hover { color: #D26BFF; }
    .admin-page-title {
        font-size: 26px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 4px;
        letter-spacing: -0.4px;
    }
    .admin-page-sub {
        font-size: 13px;
        color: rgba(255,255,255,0.4);
        margin: 0;
    }
    .btn-admin-new {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: rgba(169,0,239,0.15);
        border: 1px solid rgba(169,0,239,0.4);
        border-radius: 10px;
        color: #D26BFF;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: background .15s, border-color .15s, transform .12s;
        white-space: nowrap;
    }
    .btn-admin-new:hover {
        background: rgba(169,0,239,0.25);
        border-color: rgba(169,0,239,0.6);
        transform: translateY(-1px);
    }

    /* Table */
    .admin-table-wrap {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        overflow: hidden;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .admin-table thead tr {
        background: rgba(255,255,255,0.03);
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .admin-table th {
        padding: 13px 16px;
        color: rgba(255,255,255,0.35);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        white-space: nowrap;
    }
    .admin-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: background .15s;
    }
    .admin-table tbody tr:last-child { border-bottom: none; }
    .admin-table tbody tr:hover { background: rgba(169,0,239,0.04); }
    .admin-table td { padding: 15px 16px; vertical-align: middle; }

    .id-chip {
        font-family: monospace;
        font-size: 12px;
        color: rgba(255,255,255,0.25);
        letter-spacing: 0.5px;
    }
    .tipo-badge {
        display: inline-block;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-top: 4px;
        background: rgba(169,0,239,0.12);
        border: 1px solid rgba(169,0,239,0.25);
        color: #D26BFF;
    }
    .estado-on {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.85);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .estado-off {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.28);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .action-edit, .action-del {
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        padding: 5px 11px;
        border-radius: 7px;
        border: 1px solid;
        transition: background .15s, color .15s;
    }
    .action-edit {
        color: rgba(255,255,255,0.7);
        border-color: rgba(255,255,255,0.12);
    }
    .action-edit:hover {
        background: rgba(169,0,239,0.12);
        border-color: rgba(169,0,239,0.3);
        color: #D26BFF;
    }
    .action-del {
        color: rgba(255,100,100,0.7);
        border-color: rgba(255,100,100,0.15);
    }
    .action-del:hover {
        background: rgba(255,60,60,0.08);
        border-color: rgba(255,60,60,0.3);
        color: #ff6b6b;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255,255,255,0.3);
    }
    .empty-state__icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state__text { font-size: 15px; }

    .alert-success {
        background: rgba(169,0,239,0.08);
        border: 1px solid rgba(169,0,239,0.25);
        color: #D26BFF;
        padding: 12px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
    }
</style>

<div style="max-width: 1200px; margin: 0 auto;">

    {{-- Back --}}
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back">
        ← Volver al Dashboard
    </a>

    {{-- Header --}}
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">🛠️ Servicios B2B</h1>
            <p class="admin-page-sub">Gestión global de todos los servicios técnicos de la plataforma</p>
        </div>
        <a href="{{ route('admin.servicios.create') }}" class="btn-admin-new">
            + Nuevo Servicio
        </a>
    </div>

    {{-- Feedback --}}
    @if(session('status'))
        <div class="alert-success">✓ {{ session('status') }}</div>
    @endif

    {{-- Table --}}
    <div class="admin-table-wrap">
        @if($servicios->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">🛠️</div>
                <p class="empty-state__text">No hay servicios registrados en la plataforma.</p>
            </div>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Servicio</th>
                        <th>Ingeniero</th>
                        <th style="text-align:center;">Plazo</th>
                        <th style="text-align:center;">Revisiones</th>
                        <th style="text-align:right;">Precio</th>
                        <th style="text-align:center;">Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicios as $servicio)
                    <tr>
                        <td><span class="id-chip">#{{ str_pad($servicio->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <span style="color:#fff; font-weight:600; display:block;">{{ $servicio->titulo_servicio }}</span>
                            <span class="tipo-badge">{{ $servicio->tipo_servicio }}</span>
                        </td>
                        <td>
                            <span style="color:rgba(255,255,255,0.8); font-weight:500;">
                                {{ $servicio->usuario->nombre_usuario ?? '—' }}
                            </span>
                            <span style="display:block; font-size:12px; color:rgba(255,255,255,0.3);">
                                {{ $servicio->usuario->direccion_correo ?? '' }}
                            </span>
                        </td>
                        <td style="text-align:center; color:rgba(255,255,255,0.6);">
                            {{ $servicio->plazo_entrega_dias ? $servicio->plazo_entrega_dias . ' d.' : '—' }}
                        </td>
                        <td style="text-align:center; color:rgba(255,255,255,0.6);">
                            {{ $servicio->numero_revisiones ?? '—' }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#fff; white-space:nowrap;">
                            {{ number_format($servicio->precio_servicio, 2) }} €
                        </td>
                        <td style="text-align:center;">
                            @if($servicio->servicio_activo)
                                <span class="estado-on">Activo</span>
                            @else
                                <span class="estado-off">Pausado</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="{{ route('admin.servicios.edit', $servicio->id) }}" class="action-edit">Editar</a>
                                <a href="{{ route('admin.servicios.delete', $servicio->id) }}"
                                   onclick="return confirm('¿Eliminar este servicio de forma permanente?')"
                                   class="action-del">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
