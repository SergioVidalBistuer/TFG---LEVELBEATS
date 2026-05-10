@extends('layouts.master')
@section('title', 'Studio | Gestionar Proyecto #' . $proyecto->id)

@section('content')
@php
    $estadoOpciones = [
        'pendiente_aceptacion_ingeniero' => 'Pendiente de aceptación del ingeniero',
        'pendiente_pago_cliente' => 'Pendiente de pago del cliente',
        'pendiente_archivos' => 'Pendiente de archivos',
        'archivos_recibidos' => 'Archivos recibidos',
        'en_proceso' => 'En proceso',
        'en_revision' => 'En revisión',
        'entregado' => 'Entregado',
        'cerrado' => 'Cerrado',
        'cancelado' => 'Cancelado',
    ];
    $estadoLabel = $estadoOpciones[$proyecto->estado_proyecto] ?? 'Pendiente de aceptación';
    $cancelado = $proyecto->estado_proyecto === 'cancelado' || !empty($proyecto->cancelado_at);
    $pagado = !empty($proyecto->id_compra) || in_array($proyecto->estado_proyecto, ['en_proceso', 'en_revision', 'entregado', 'cerrado']);
    $ingenieroAceptado = !empty($proyecto->ingeniero_aceptado_at);
    $clienteAceptado = !empty($proyecto->cliente_aceptado_at) || $pagado;
    $puedeCancelar = !$cancelado && !$pagado;
@endphp
<div class="studio-page studio-page--form">
    <div class="studio-form-head">
        <a class="btn btn--ghost" href="{{ route('studio.proyectos.index') }}">Volver</a>
        <div>
            <p class="studio-eyebrow">Studio · Encargos</p>
            <h1>{{ $proyecto->titulo_proyecto }}</h1>
            <p class="muted">Gestiona estado, notas, archivos y conversación del encargo.</p>
        </div>
    </div>

    <section class="studio-form-panel project-workspace">
        <div class="project-summary">
            <div>
                <span>Cliente</span>
                <strong>{{ $proyecto->cliente->nombre_usuario ?? 'Desconocido' }}</strong>
                <small>{{ $proyecto->cliente->direccion_correo ?? '' }}</small>
            </div>
            <div>
                <span>Servicio</span>
                <strong>{{ $proyecto->servicio->titulo_servicio ?? '-' }}</strong>
                <small>{{ $proyecto->fecha_creacion ?? '-' }}</small>
            </div>
        </div>

        <form class="studio-form" method="POST" action="{{ route('studio.proyectos.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $proyecto->id }}">

            <div class="row g-3">
                <div class="col-md-5">
                    <div class="studio-field">
                        <label for="estado_proyecto">Estado del proyecto</label>
                        @php
                            $estadoSeleccionado = old('estado_proyecto', $proyecto->estado_proyecto);
                        @endphp
                        <div class="project-status-dropdown" data-project-status-dropdown>
                            <input id="estado_proyecto" type="hidden" name="estado_proyecto" value="{{ $estadoSeleccionado }}">
                            <button type="button" class="project-status-dropdown__trigger" aria-expanded="false">
                                <span data-project-status-label>{{ $estadoOpciones[$estadoSeleccionado] ?? 'Selecciona estado' }}</span>
                            </button>
                            <div class="project-status-dropdown__menu" role="listbox">
                                @foreach($estadoOpciones as $valor => $etiqueta)
                                    <button
                                        type="button"
                                        class="project-status-dropdown__option {{ $estadoSeleccionado === $valor ? 'is-selected' : '' }}"
                                        data-project-status-value="{{ $valor }}"
                                        data-project-status-text="{{ $etiqueta }}"
                                        role="option"
                                        aria-selected="{{ $estadoSeleccionado === $valor ? 'true' : 'false' }}"
                                    >
                                        {{ $etiqueta }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('estado_proyecto')
                            <small class="text-danger">Selecciona un estado válido.</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="studio-field">
                        <label for="notas_proyecto">Notas privadas</label>
                        <textarea id="notas_proyecto" name="notas_proyecto" class="form-control form-lb__textarea" placeholder="Notas internas del trabajo...">{{ old('notas_proyecto', $proyecto->notas_proyecto ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="studio-form-actions">
                <button type="submit" class="btn btn--primary">Actualizar progreso</button>
            </div>
        </form>

        @include('partials.project-files')

        <div class="project-chat">
            <h4>Mensajes con el cliente</h4>
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
                <input type="text" name="contenido_mensaje" class="form-control form-lb__input" placeholder="Escribe un mensaje al cliente..." required autocomplete="off">
                <button type="submit" class="btn btn--primary">Enviar</button>
            </form>
        </div>

        <div class="service-flow-panel">
            <div class="service-flow-panel__head">
                <div>
                    <p class="studio-eyebrow">Flujo del servicio</p>
                    <h4>Confirmación y pago</h4>
                    <p class="muted">Acepta el encargo para que el cliente pueda confirmar términos y pagar el servicio.</p>
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
                    <button class="btn btn--ghost" disabled>Servicio aceptado por el ingeniero</button>
                @else
                    <form method="POST" action="{{ route('proyectos.aceptarIngeniero', $proyecto) }}">
                        @csrf
                        <button type="submit" class="btn btn--primary">Aceptar Servicio</button>
                    </form>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-project-status-dropdown]').forEach(function (dropdown) {
        const trigger = dropdown.querySelector('.project-status-dropdown__trigger');
        const input = dropdown.querySelector('input[name="estado_proyecto"]');
        const label = dropdown.querySelector('[data-project-status-label]');
        const options = dropdown.querySelectorAll('[data-project-status-value]');

        trigger.addEventListener('click', function () {
            const isOpen = dropdown.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                input.value = option.dataset.projectStatusValue;
                label.textContent = option.dataset.projectStatusText;
                options.forEach(function (item) {
                    item.classList.remove('is-selected');
                    item.setAttribute('aria-selected', 'false');
                });
                option.classList.add('is-selected');
                option.setAttribute('aria-selected', 'true');
                dropdown.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('click', function (event) {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                dropdown.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    });
});
</script>
@endsection
