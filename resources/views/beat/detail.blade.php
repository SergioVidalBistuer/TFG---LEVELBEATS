@extends('layouts.master')

@section('title', $beat->titulo_beat)

@section('content')
@php
    $precioBase = (float) $beat->precio_base_licencia;
@endphp

<div class="product-detail">
    <section class="product-detail__main">
        <div class="product-detail__content">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                <span class="badge">Beat</span>
                {{-- Botón Guardar en detalle --}}
                @include('partials.btn-guardado', [
                    'tipo'    => 'beat',
                    'id'      => $beat->id,
                    'guardado'=> $estaGuardado,
                    'compact' => false,
                ])
            </div>
            <h1>{{ $beat->titulo_beat }}</h1>
            <p class="muted">Género: {{ $beat->genero_musical ?? 'No especificado' }}</p>
            <p class="muted">Estado de ánimo: {{ $beat->estado_de_animo ?? '-' }}</p>

            <div class="panel panel--dark product-audio">
                <h2>Previsualización</h2>
                <audio controls class="audio-player-custom">
                    <source src="{{ asset($beat->url_audio_previsualizacion ?? 'media/audio/demo.mp3') }}" type="audio/mpeg">
                </audio>
            </div>

            @if($beat->colecciones && $beat->colecciones->count())
                <div class="product-related">
                    <h2>Colecciones</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($beat->colecciones as $col)
                            <a href="{{ route('coleccion.detail', ['id' => $col->id]) }}" class="btn btn--ghost btn-sm">
                                {{ $col->titulo_coleccion }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="product-detail__media">
            <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}" alt="Portada {{ $beat->titulo_beat }}">
        </div>
    </section>

    <section class="license-panel" data-license-panel data-base-price="{{ number_format($precioBase, 2, '.', '') }}">
        <div class="license-panel__head">
            <div>
                <p class="studio-eyebrow">Licencias</p>
                <h2>Elige tu licencia</h2>
                <p class="muted">El precio final se calcula como precio base del producto más el importe de la licencia.</p>
            </div>
            @if($exclusivaVendida)
                <span class="license-status">Exclusiva vendida</span>
            @endif
        </div>

        @if($licenciasCompra->isEmpty())
            <div class="account-feedback account-feedback--error">
                No hay licencias configuradas para compras. Revisa la tabla licencia.
            </div>
        @else
            <form action="{{ route('carrito.addBeat') }}" method="POST" class="license-form">
                @csrf
                <input type="hidden" name="id" value="{{ $beat->id }}">

                <div class="license-grid">
                    @foreach($licenciasCompra as $licencia)
                        @php
                            $spec = \App\Support\LicenciaCompra::spec($licencia);
                            $precioLicencia = \App\Support\LicenciaCompra::precio($licencia);
                            $disabled = $licencia->tipo_licencia === 'exclusiva' && $exclusivaVendida;
                            $checked = old('licencia_id') ? (int) old('licencia_id') === $licencia->id : $licencia->tipo_licencia === 'basica';
                        @endphp
                        <label class="license-card {{ $disabled ? 'is-disabled' : '' }}">
                            <input
                                type="radio"
                                name="licencia_id"
                                value="{{ $licencia->id }}"
                                data-license-price="{{ number_format($precioLicencia, 2, '.', '') }}"
                                data-license-name="{{ $spec['titulo'] }}"
                                {{ $checked && !$disabled ? 'checked' : '' }}
                                {{ $disabled ? 'disabled' : '' }}
                            >
                            <span class="license-card__top">
                                <strong>{{ $spec['titulo'] }}</strong>
                                <em>{{ number_format($precioLicencia, 2, ',', '.') }} €</em>
                            </span>
                            <span class="license-card__format">{{ $spec['formato'] }}</span>
                            <span class="license-card__summary">{{ $spec['resumen'] }}</span>
                            <small>{{ $disabled ? 'No disponible para nuevas compras exclusivas.' : $spec['derechos'] }}</small>
                        </label>
                    @endforeach
                </div>

                <div class="license-total">
                    <div>
                        <span>Precio base</span>
                        <strong>{{ number_format($precioBase, 2, ',', '.') }} €</strong>
                    </div>
                    <div>
                        <span>Licencia</span>
                        <strong data-license-current>0,00 €</strong>
                    </div>
                    <div class="license-total__final">
                        <span>Total</span>
                        <strong data-license-total>{{ number_format($precioBase, 2, ',', '.') }} €</strong>
                    </div>
                </div>

                @if(auth()->check() && !auth()->user()->esAdmin())
                    <button class="btn btn--primary" type="submit">Añadir al carrito</button>
                @elseif(auth()->check() && auth()->user()->esAdmin())
                    <p class="muted mb-0">El usuario administrador no necesita comprar productos.</p>
                @else
                    <a class="btn btn--primary" href="{{ route('login') }}">Inicia sesión para comprar</a>
                @endif
            </form>
        @endif
    </section>

    @if(auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $beat->id_usuario))
        <div class="admin-actions d-flex gap-2">
            <a class="btn btn--ghost" href="{{ route('beat.edit', $beat->id) }}">Editar</a>
            <a class="btn btn--ghost btn--danger" href="{{ route('beat.delete', $beat->id) }}" onclick="return confirm('¿Seguro que quieres eliminar este beat?')">
                Eliminar
            </a>
        </div>
    @endif

    <div class="product-detail__back">
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Volver al listado</a>
    </div>
</div>

<script>
document.querySelectorAll('[data-license-panel]').forEach(function(panel) {
    const base = Number(panel.dataset.basePrice || 0);
    const radios = panel.querySelectorAll('input[name="licencia_id"]');
    const current = panel.querySelector('[data-license-current]');
    const total = panel.querySelector('[data-license-total]');

    function format(value) {
        return value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    function update() {
        const selected = panel.querySelector('input[name="licencia_id"]:checked');
        const licensePrice = selected ? Number(selected.dataset.licensePrice || 0) : 0;
        if (current) current.textContent = format(licensePrice);
        if (total) total.textContent = format(base + licensePrice);
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', update);
    });

    update();
});
</script>
@endsection
