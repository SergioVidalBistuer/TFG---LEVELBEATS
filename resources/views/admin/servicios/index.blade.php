@extends('layouts.master')

@section('title', 'Admin - Servicios')

@section('content')
<div class="admin-page">
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Servicios</h1>
            <p>Catálogo técnico publicado por ingenieros de la plataforma.</p>
        </div>
        <a href="{{ route('admin.servicios.create') }}" class="btn btn--primary">Nuevo servicio</a>
    </header>

    @if(session('status'))
        <div class="admin-feedback">{{ session('status') }}</div>
    @endif

    <section class="admin-table-card">
        @if($servicios->isEmpty())
            <div class="admin-empty">No hay servicios registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Ingeniero</th>
                            <th class="text-center">Plazo</th>
                            <th class="text-center">Revisiones</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicios as $servicio)
                            <tr>
                                <td><span class="admin-id">#{{ str_pad($servicio->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <strong>{{ $servicio->titulo_servicio }}</strong>
                                    <div><span class="admin-badge">{{ ucfirst($servicio->tipo_servicio) }}</span></div>
                                </td>
                                <td>
                                    <strong>{{ $servicio->usuario->nombre_usuario ?? '-' }}</strong>
                                    <div class="admin-muted">{{ $servicio->usuario->direccion_correo ?? '' }}</div>
                                </td>
                                <td class="text-center admin-muted">{{ $servicio->plazo_entrega_dias ? $servicio->plazo_entrega_dias . ' días' : '-' }}</td>
                                <td class="text-center admin-muted">{{ $servicio->numero_revisiones ?? '-' }}</td>
                                <td class="text-end fw-bold">{{ number_format($servicio->precio_servicio, 2, ',', '.') }} €</td>
                                <td class="text-center">
                                    <span class="admin-badge {{ $servicio->servicio_activo ? 'admin-badge--ok' : 'admin-badge--muted' }}">
                                        {{ $servicio->servicio_activo ? 'Activo' : 'Pausado' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.servicios.edit', $servicio->id) }}">Editar</a>
                                        <a class="admin-actions__danger" href="{{ route('admin.servicios.delete', $servicio->id) }}" onclick="return confirm('¿Eliminar este servicio de forma permanente?')">Borrar</a>
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
