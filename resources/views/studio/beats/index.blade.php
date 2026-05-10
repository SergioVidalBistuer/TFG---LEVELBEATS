@extends('layouts.master')
@section('title', 'Studio | Mis Beats')

@section('content')
<div class="studio-page">
    <div class="studio-page__head">
        <div>
            <p class="studio-eyebrow">Studio</p>
            <h1>Mis Beats</h1>
            <p class="muted">Gestiona tu inventario de beats publicados y ocultos.</p>
        </div>
        <a href="{{ route('studio.beats.create') }}" class="btn btn--primary">Subir nuevo beat</a>
    </div>

    <section class="studio-panel">
        @if($beats->count() === 0)
            <div class="studio-empty">
                <h2>No tienes beats todavía</h2>
                <p class="muted">Sube tu primer beat para empezar a construir tu catálogo en LevelBeats.</p>
                <a class="btn btn--primary" href="{{ route('studio.beats.create') }}">Subir beat</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Género / BPM</th>
                            <th class="text-end">Precio base</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beats as $beat)
                        <tr>
                            <td>
                                <strong>{{ $beat->titulo_beat }}</strong>
                            </td>
                            <td class="studio-table__muted">
                                {{ $beat->genero_musical ?: 'Sin género' }}
                                @if($beat->tempo_bpm)
                                    · {{ $beat->tempo_bpm }} BPM
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ number_format($beat->precio_base_licencia, 2, ',', '.') }} €</td>
                            <td class="text-center">
                                @if($beat->activo_publicado)
                                    <span class="studio-badge studio-badge--public">Público</span>
                                @else
                                    <span class="studio-badge">Oculto</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="studio-actions">
                                    <a href="{{ route('beat.detail', $beat->id) }}">Ver</a>
                                    <a href="{{ route('studio.beats.edit', $beat->id) }}">Editar</a>
                                    <a class="studio-actions__danger" href="{{ route('studio.beats.delete', $beat->id) }}" onclick="return confirm('¿Seguro que deseas eliminar el beat del inventario?')">Eliminar</a>
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
