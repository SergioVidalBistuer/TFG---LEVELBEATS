@extends('layouts.master')
@section('title', 'Mis Encargos')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">Mi Área</p>
            <h1>Mis encargos</h1>
            <p class="muted">Seguimiento de los servicios técnicos que has solicitado a ingenieros.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('servicio.index') }}">Explorar servicios</a>
    </div>

    <section class="studio-panel">
        @if($proyectos->count() === 0)
            <div class="studio-empty">
                <h2>Aún no has encargado ningún servicio</h2>
                <p class="muted">Cuando solicites un servicio, podrás seguir aquí el estado, mensajes y archivos compartidos.</p>
                <a class="btn btn--primary" href="{{ route('servicio.index') }}">Ver servicios</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Trabajo</th>
                            <th>Ingeniero</th>
                            <th>Servicio</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
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
                        @endphp
                        <tr>
                            <td><strong>{{ $proyecto->titulo_proyecto }}</strong></td>
                            <td>{{ $proyecto->servicio->usuario->nombre_usuario ?? 'Desconocido' }}</td>
                            <td class="studio-table__muted">{{ $proyecto->servicio->titulo_servicio ?? '-' }}</td>
                            <td class="text-center studio-table__muted">{{ $proyecto->fecha_creacion ? \Carbon\Carbon::parse($proyecto->fecha_creacion)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="studio-badge {{ $estadoFinalizado ? 'studio-badge--public' : '' }}">{{ $estadoLabel }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('usuario.encargos.detail', $proyecto->id) }}" class="btn btn--ghost btn-sm">Consultar</a>
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
