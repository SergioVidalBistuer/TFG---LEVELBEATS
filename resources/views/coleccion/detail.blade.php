@extends('layouts.master')

@section('title', $coleccion->titulo_coleccion)

@section('content')
@php
    $precioBase = (float) $coleccion->precio;
    $portadaColeccion = $coleccion->beats->first()?->url_portada_beat;
@endphp

<div class="product-detail">
    <section class="product-detail__main">
        <div class="product-detail__content">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                <span class="badge">Colección</span>
                {{-- Botón Guardar en detalle --}}
                @include('partials.btn-guardado', [
                    'tipo'    => 'coleccion',
                    'id'      => $coleccion->id,
                    'guardado'=> $estaGuardado,
                    'compact' => false,
                ])
            </div>
            <h1>{{ $coleccion->titulo_coleccion }}</h1>
            <p class="muted">Tipo: {{ $coleccion->tipo_coleccion ?? 'No especificado' }}</p>
            <p class="muted">Género: {{ $coleccion->estilo_genero ?? 'No especificado' }}</p>
            <p class="muted">Incluye {{ $coleccion->beats->count() }} beats</p>

            @if($coleccion->descripcion_coleccion)
                <div class="panel panel--dark product-audio">
                    <h2>Descripción</h2>
                    <p>{{ $coleccion->descripcion_coleccion }}</p>
                </div>
            @endif
        </div>

        <div class="product-detail__media">
            @if($portadaColeccion)
                <img src="{{ asset($portadaColeccion) }}" alt="Portada {{ $coleccion->titulo_coleccion }}">
            @else
                <div class="product-detail__placeholder">{{ strtoupper(substr($coleccion->titulo_coleccion, 0, 1)) }}</div>
            @endif
        </div>
    </section>

    <section class="license-panel" data-license-panel data-base-price="{{ number_format($precioBase, 2, '.', '') }}">
        <div class="license-panel__head">
            <div>
                <p class="studio-eyebrow">Licencias</p>
                <h2>Licencia para la colección</h2>
                <p class="muted">La licencia seleccionada se aplica a todos los beats incluidos en la colección.</p>
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
            <form action="{{ route('carrito.addColeccion') }}" method="POST" class="license-form">
                @csrf
                <input type="hidden" name="id" value="{{ $coleccion->id }}">

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
                        <span>Precio base colección</span>
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
                    <button class="btn btn--primary" type="submit">Añadir colección al carrito</button>
                @elseif(auth()->check() && auth()->user()->esAdmin())
                    <p class="muted mb-0">El usuario administrador no necesita comprar productos.</p>
                @else
                    <a href="{{ route('login') }}" class="btn btn--primary">Inicia sesión para comprar</a>
                @endif
            </form>
        @endif
    </section>

    @if(auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $coleccion->id_usuario))
        <div class="admin-actions d-flex gap-2">
            <a class="btn btn--ghost" href="{{ route('coleccion.edit', $coleccion->id) }}">Editar</a>
            <a class="btn btn--ghost btn--danger" href="{{ route('coleccion.delete', $coleccion->id) }}" onclick="return confirm('¿Seguro que quieres eliminar esta colección?')">
                Eliminar
            </a>
        </div>
    @endif

    <section class="area-section">
        <div class="area-section__head">
            <h2>Beats de esta colección</h2>
            <span>{{ $coleccion->beats->count() }} incluidos</span>
        </div>

        @if($coleccion->beats->count())
            <div class="grid grid--4">
                @foreach($coleccion->beats as $beat)
                    <article class="card card--clickable" data-card-link="{{ route('beat.detail', ['id' => $beat->id]) }}" tabindex="0">
                        <div class="card__media">
                            <img src="{{ asset($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}" alt="Portada {{ $beat->titulo_beat }}">
                        </div>

                        <div class="card__body">
                            <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                            <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>

                            <div class="card__foot">
                                <span class="price">{{ number_format($beat->precio_base_licencia, 2, ',', '.') }} €</span>
                                <div class="card__actions">
                                    <a class="btn btn--ghost" href="{{ route('beat.detail', ['id' => $beat->id]) }}">Ver</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="studio-panel">
                <div class="studio-empty studio-empty--compact">
                    <p class="muted">Esta colección aún no tiene beats asociados.</p>
                </div>
            </div>
        @endif
    </section>

    <div class="product-detail__back">
        <a class="btn btn--ghost" href="{{ route('coleccion.index') }}">Volver a Colecciones</a>
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
