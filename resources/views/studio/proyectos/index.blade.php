@extends('layouts.master')
@section('title', 'Studio | Encargos')

@section('content')
<div class="studio-page">
    <div class="studio-page__head">
        <div>
            <p class="studio-eyebrow">Studio</p>
            <h1>Encargos</h1>
            <p class="muted">Trabajos contratados por clientes sobre tus servicios técnicos.</p>
        </div>
    </div>

    <section class="studio-panel">
        @if($proyectos->count() === 0)
            <div class="studio-empty">
                <h2>No tienes encargos activos</h2>
                <p class="muted">Cuando un cliente solicite uno de tus servicios, aparecerá aquí.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end studio-project-actions-cell">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $proyecto)
                        @php
                            $estadoLabel = [
                                'pendiente_aceptacion_ingeniero' => 'Pendiente de aceptación',
                                'pendiente_pago_cliente' => 'Pendiente de pago',
                                'pendiente_archivos' => 'Pendiente de archivos',
                                'archivos_recibidos' => 'Archivos recibidos',
                                'en_proceso' => 'En proceso',
                                'en_revision' => 'En revisión',
                                'entregado' => 'Entregado',
                                'cerrado' => 'Cerrado',
                                'cancelado' => 'Cancelado',
                            ][$proyecto->estado_proyecto] ?? 'Pendiente';
                            $estadoFinalizado = in_array($proyecto->estado_proyecto, ['entregado', 'cerrado']);
                            $puedeEliminar = in_array($proyecto->estado_proyecto, ['cancelado', 'cerrado']);
                        @endphp
                        <tr>
                            <td><strong>#{{ $proyecto->id }} · {{ $proyecto->titulo_proyecto }}</strong></td>
                            <td>{{ $proyecto->cliente->nombre_usuario ?? 'Desconocido' }}</td>
                            <td class="studio-table__muted">{{ $proyecto->servicio->titulo_servicio ?? '-' }}</td>
                            <td class="text-center studio-table__muted">{{ $proyecto->fecha_creacion ? \Carbon\Carbon::parse($proyecto->fecha_creacion)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="studio-badge {{ $estadoFinalizado ? 'studio-badge--public' : '' }}">{{ $estadoLabel }}</span>
                            </td>
                            <td class="text-end studio-project-actions-cell">
                                <div class="studio-project-actions">
                                    <a href="{{ route('studio.proyectos.edit', $proyecto->id) }}" class="btn btn--primary btn-sm">Gestionar</a>
                                    @if($puedeEliminar)
                                        <form method="POST" action="{{ route('studio.proyectos.destroy', $proyecto) }}" onsubmit="return confirm('¿Seguro que quieres eliminar este encargo? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn--ghost btn--danger btn-sm">Eliminar</button>
                                        </form>
                                    @endif
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
