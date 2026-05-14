@extends('layouts.master')

@section('title', 'Admin - Beats')

@section('content')
<div class="admin-page">
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Beats</h1>
            <p>Catálogo musical publicado por productores.</p>
        </div>
    </header>

    <section class="admin-table-card">
        @if($beats->isEmpty())
            <div class="admin-empty">No hay beats registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Beat</th>
                            <th>Productor</th>
                            <th>Género</th>
                            <th class="text-center">BPM</th>
                            <th class="text-end">Precio base</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end admin-actions-cell">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beats as $beat)
                            <tr>
                                <td><span class="admin-id">#{{ str_pad($beat->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td><strong>{{ $beat->titulo_beat }}</strong></td>
                                <td class="admin-muted">{{ $beat->usuario->nombre_usuario ?? '-' }}</td>
                                <td class="admin-muted">{{ $beat->genero_musical ?? '-' }}</td>
                                <td class="text-center admin-muted">{{ $beat->tempo_bpm ?? '-' }}</td>
                                <td class="text-end fw-bold">{{ number_format((float) $beat->precio_base_licencia, 2, ',', '.') }} €</td>
                                <td class="text-center">
                                    <span class="admin-badge {{ $beat->activo_publicado ? 'admin-badge--ok' : 'admin-badge--muted' }}">
                                        {{ $beat->activo_publicado ? 'Publicado' : 'Oculto' }}
                                    </span>
                                </td>
                                <td class="text-end admin-actions-cell">
                                    <div class="admin-actions">
                                        <a href="{{ route('beat.detail', $beat->id) }}">Ver</a>
                                        <a href="{{ route('admin.beats.edit', $beat->id) }}">Editar</a>
                                        <a class="admin-actions__danger" href="{{ route('beat.delete', $beat->id) }}" onclick="return confirm('¿Eliminar este beat de forma permanente?')">Borrar</a>
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
