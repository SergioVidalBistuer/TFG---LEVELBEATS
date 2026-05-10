@extends('layouts.master')
@section('title', 'Studio | Mis Servicios')

@section('content')
<div class="studio-page">
    <div class="studio-page__head">
        <div>
            <p class="studio-eyebrow">Studio</p>
            <h1>Mis Servicios</h1>
            <p class="muted">Gestiona tus servicios técnicos, precios, plazos y disponibilidad.</p>
        </div>
        <a href="{{ route('studio.servicios.create') }}" class="btn btn--primary">Publicar servicio</a>
    </div>

    <section class="studio-panel">
        @if($servicios->count() === 0)
            <div class="studio-empty">
                <h2>No tienes servicios publicados</h2>
                <p class="muted">Crea tu primera oferta técnica para empezar a recibir encargos.</p>
                <a class="btn btn--primary" href="{{ route('studio.servicios.create') }}">Crear servicio</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Título / Tipo</th>
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
                            <td>
                                <strong>{{ $servicio->titulo_servicio }}</strong>
                                <div class="studio-table__muted">{{ ucfirst($servicio->tipo_servicio) }}</div>
                            </td>
                            <td class="text-center">{{ $servicio->plazo_entrega_dias ? $servicio->plazo_entrega_dias . ' días' : '-' }}</td>
                            <td class="text-center">{{ $servicio->numero_revisiones ?? '-' }}</td>
                            <td class="text-end fw-bold">{{ number_format($servicio->precio_servicio, 2, ',', '.') }} €</td>
                            <td class="text-center">
                                @if($servicio->servicio_activo)
                                    <span class="studio-badge studio-badge--public">Activo</span>
                                @else
                                    <span class="studio-badge">Pausado</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="studio-actions">
                                    <a href="{{ route('servicio.detail', $servicio->id) }}">Ver</a>
                                    <a href="{{ route('studio.servicios.edit', $servicio->id) }}">Editar</a>
                                    <a class="studio-actions__danger" href="{{ route('studio.servicios.delete', $servicio->id) }}" onclick="return confirm('¿Seguro que deseas eliminar el servicio?')">Eliminar</a>
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
