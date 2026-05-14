@extends('layouts.master')

@section('title', 'Admin - Colecciones')

@section('content')
<div class="admin-page">
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Colecciones</h1>
            <p>Packs y agrupaciones creadas por productores.</p>
        </div>
    </header>

    <section class="admin-table-card">
        @if($colecciones->isEmpty())
            <div class="admin-empty">No hay colecciones registradas.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Colección</th>
                            <th>Productor</th>
                            <th>Tipo</th>
                            <th>Género</th>
                            <th class="text-center">Beats</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colecciones as $coleccion)
                            <tr>
                                <td><span class="admin-id">#{{ str_pad($coleccion->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td><strong>{{ $coleccion->titulo_coleccion }}</strong></td>
                                <td class="admin-muted">{{ $coleccion->usuario->nombre_usuario ?? '-' }}</td>
                                <td><span class="admin-badge">{{ ucfirst($coleccion->tipo_coleccion ?? '-') }}</span></td>
                                <td class="admin-muted">{{ $coleccion->estilo_genero ?? '-' }}</td>
                                <td class="text-center admin-muted">{{ $coleccion->beats_count }}</td>
                                <td class="text-end fw-bold">{{ number_format((float) $coleccion->precio, 2, ',', '.') }} €</td>
                                <td class="text-center">
                                    <span class="admin-badge {{ $coleccion->activo_publicado ? 'admin-badge--ok' : 'admin-badge--muted' }}">
                                        {{ $coleccion->activo_publicado ? 'Publicada' : 'Oculta' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="admin-actions">
                                        <a href="{{ route('coleccion.detail', $coleccion->id) }}">Ver</a>
                                        <a href="{{ route('coleccion.edit', $coleccion->id) }}">Editar</a>
                                        <a class="admin-actions__danger" href="{{ route('coleccion.delete', $coleccion->id) }}" onclick="return confirm('¿Eliminar esta colección de forma permanente?')">Borrar</a>
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
