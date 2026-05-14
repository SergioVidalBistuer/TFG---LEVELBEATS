@extends('layouts.master')

@section('title', 'Admin - Proyectos')

@section('content')
@php
    $estadoLabel = fn($estado) => ucfirst(str_replace('_', ' ', $estado ?? 'pendiente'));
@endphp

<div class="admin-page">
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Proyectos</h1>
            <p>Encargos y trabajos generados a partir de servicios técnicos.</p>
        </div>
    </header>

    <section class="admin-table-card">
        @if($proyectos->isEmpty())
            <div class="admin-empty">No hay proyectos registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Ingeniero</th>
                            <th>Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $proyecto)
                            <tr>
                                <td><span class="admin-id">#{{ str_pad($proyecto->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td><strong>{{ $proyecto->titulo_proyecto }}</strong></td>
                                <td class="admin-muted">{{ $proyecto->cliente->nombre_usuario ?? '-' }}</td>
                                <td class="admin-muted">{{ $proyecto->servicio->titulo_servicio ?? '-' }}</td>
                                <td class="admin-muted">{{ $proyecto->servicio->usuario->nombre_usuario ?? '-' }}</td>
                                <td class="admin-muted">{{ $proyecto->fecha_creacion ? \Carbon\Carbon::parse($proyecto->fecha_creacion)->format('d/m/Y') : '-' }}</td>
                                <td class="text-center">
                                    <span class="admin-badge {{ in_array($proyecto->estado_proyecto, ['cancelado'], true) ? 'admin-badge--danger' : (in_array($proyecto->estado_proyecto, ['cerrado', 'entregado'], true) ? 'admin-badge--ok' : 'admin-badge--accent') }}">
                                        {{ $estadoLabel($proyecto->estado_proyecto) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-actions">
                                        <a href="{{ route('studio.proyectos.edit', $proyecto->id) }}">Gestionar</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
