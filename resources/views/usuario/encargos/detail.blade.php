@extends('layouts.master')
@section('title', 'Detalle del Encargo #' . $proyecto->id)

@section('content')
@php
    $estadoLabel = [
        'pendiente_aceptacion_ingeniero' => 'Pendiente de aceptación del ingeniero',
        'pendiente_pago_cliente' => 'Pendiente de pago del cliente',
        'pendiente_archivos' => 'Pendiente de archivos',
        'archivos_recibidos' => 'Archivos recibidos',
        'en_proceso' => 'En proceso',
        'en_revision' => 'En revisión',
        'entregado' => 'Entregado',
        'cerrado' => 'Cerrado',
        'cancelado' => 'Cancelado',
    ][$proyecto->estado_proyecto] ?? 'Pendiente de aceptación';
    $estadoFinalizado = in_array($proyecto->estado_proyecto, ['entregado', 'cerrado']);
    $cancelado = $proyecto->estado_proyecto === 'cancelado' || !empty($proyecto->cancelado_at);
    $pagado = !empty($proyecto->id_compra) || in_array($proyecto->estado_proyecto, ['en_proceso', 'en_revision', 'entregado', 'cerrado']);
    $ingenieroAceptado = !empty($proyecto->ingeniero_aceptado_at);
    $clienteAceptado = !empty($proyecto->cliente_aceptado_at) || $pagado;
    $puedeCancelar = !$cancelado && !$pagado;
@endphp

<div class="area-page area-page--narrow">
    <div class="studio-form-head">
        <a class="btn btn--ghost" href="{{ route('usuario.encargos.index') }}">Volver</a>
        <div>
            <p class="studio-eyebrow">Mi Área · Encargos</p>
            <h1>{{ $proyecto->titulo_proyecto }}</h1>
            <p class="muted">Consulta progreso, archivos y conversación con el ingeniero.</p>
        </div>
    </div>

    <section class="studio-form-panel project-workspace">
        <div class="project-summary">
            <div>
                <span>Ingeniero</span>
                <strong>{{ $proyecto->servicio->usuario->nombre_usuario ?? 'Desconocido' }}</strong>
                <small>{{ $proyecto->servicio->titulo_servicio ?? '-' }}</small>
            </div>
            <div>
                <span>Estado</span>
                <strong>{{ $estadoLabel }}</strong>
                <small>{{ $proyecto->fecha_creacion ?? '-' }}</small>
            </div>
        </div>

        @include('partials.project-files')

        <div class="project-chat">
            <h4>Mensajes del trabajo</h4>
            <div class="project-chat__messages">
                @forelse($proyecto->mensajes as $msg)
                    @php $esMio = $msg->id_usuario_emisor === auth()->id(); @endphp
                    <div class="project-chat__row {{ $esMio ? 'is-mine' : '' }}">
                        <span>{{ $msg->emisor->nombre_usuario ?? '...' }} · {{ \Carbon\Carbon::parse($msg->fecha_envio)->format('d/m H:i') }}</span>
                        <div class="{{ $esMio ? 'chat-bubble--mine' : 'chat-bubble--other' }}">
                            {{ $msg->contenido_mensaje }}
                        </div>
                    </div>
                @empty
                    <p class="muted text-center">No hay mensajes en este panel todavía.</p>
                @endforelse
            </div>

            <form class="project-inline-form" action="{{ route('mensajes.proyecto.enviar', $proyecto->id) }}" method="POST">
                @csrf
                <input type="text" name="contenido_mensaje" class="form-control form-lb__input" placeholder="Escribe un mensaje al ingeniero..." required autocomplete="off">
                <button type="submit" class="btn btn--primary">Enviar</button>
            </form>
        </div>

        <div class="service-flow-panel">
            <div class="service-flow-panel__head">
                <div>
                    <p class="studio-eyebrow">Flujo del servicio</p>
                    <h4>Confirmación y pago</h4>
                    <p class="muted">El encargo solo avanza cuando el ingeniero acepta y el cliente realiza el pago.</p>
                </div>
                <span class="studio-badge {{ $pagado ? 'studio-badge--public' : '' }} {{ $cancelado ? 'studio-badge--danger' : '' }}">{{ $estadoLabel }}</span>
            </div>

            <div class="service-flow-steps">
                <div class="service-flow-step {{ $ingenieroAceptado ? 'is-done' : '' }}">
                    <span>Ingeniero</span>
                    <strong>{{ $ingenieroAceptado ? 'Servicio aceptado' : 'Pendiente de aceptación' }}</strong>
                </div>
                <div class="service-flow-step {{ $clienteAceptado ? 'is-done' : '' }}">
                    <span>Cliente</span>
                    <strong>{{ $clienteAceptado ? 'Aceptado y pagado' : 'Pendiente de pago' }}</strong>
                </div>
            </div>

            <div class="service-flow-actions">
                @if($cancelado)
                    <button class="btn btn--ghost" disabled>Servicio cancelado</button>
                @elseif($pagado)
                    <button class="btn btn--ghost" disabled>Servicio pagado y en proceso</button>
                @elseif($ingenieroAceptado)
                    <form method="POST" action="{{ route('proyectos.aceptarPagar', $proyecto) }}">
                        @csrf
                        <button type="submit" class="btn btn--primary">Aceptar y Pagar Servicio</button>
                    </form>
                @else
                    <button class="btn btn--ghost" disabled>Pendiente de aceptación del ingeniero</button>
                @endif

                @if($puedeCancelar)
                    <form method="POST" action="{{ route('proyectos.cancelarServicio', $proyecto) }}" onsubmit="return confirm('¿Seguro que quieres cancelar este servicio?');">
                        @csrf
                        <button type="submit" class="btn btn--ghost btn--danger">Cancelar Servicio</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
