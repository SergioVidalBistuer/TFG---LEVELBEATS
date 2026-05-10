@extends('layouts.master')

@section('title', 'Servicios de Ingeniería – LevelBeats')

@section('hero')
    <section class="lb-hero lb-hero--servicios">
        <div class="lb-hero__inner container">
            <h1 class="lb-hero__title">Servicios</h1>
        </div>
    </section>
@endsection

@section('content')
    {{-- FILTROS --}}
    @php
        $filtrosActivos = collect(request()->only(['q', 'tipo', 'precio_min', 'precio_max', 'plazo_max', 'revisiones_min']))
            ->filter(fn($value) => filled($value))
            ->isNotEmpty();
    @endphp

    <details class="catalog-filters" {{ $filtrosActivos ? 'open' : '' }}>
        <summary class="catalog-filters__summary">
            <span>Filtros</span>
            <span class="catalog-filters__chevron">⌄</span>
        </summary>

        <form class="catalog-filters__form" method="GET" action="{{ route('servicio.index') }}">
            <div class="catalog-filters__field catalog-filters__field--wide">
                <label for="servicio-q">Buscar por título</label>
                <input class="form-control" id="servicio-q" type="search" name="q" value="{{ request('q') }}" placeholder="Ej. mezcla vocal">
            </div>

            <div class="catalog-filters__field">
                <label>Tipo de servicio</label>
                <details class="catalog-filter-dropdown">
                    <summary>
                        <span>{{ request('tipo') ? ucfirst(request('tipo')) : 'Todos' }}</span>
                        <span class="catalog-filter-dropdown__chevron"></span>
                    </summary>
                    <div class="catalog-filter-dropdown__menu">
                        <label class="catalog-filter-dropdown__item {{ request('tipo') ? '' : 'is-active' }}">
                            <input type="radio" name="tipo" value="" {{ request('tipo') ? '' : 'checked' }}>
                            Todos
                        </label>
                    @foreach($opcionesFiltro['tipos'] as $tipo)
                        <label class="catalog-filter-dropdown__item {{ request('tipo') === $tipo ? 'is-active' : '' }}">
                            <input type="radio" name="tipo" value="{{ $tipo }}" {{ request('tipo') === $tipo ? 'checked' : '' }}>
                            {{ ucfirst($tipo) }}
                        </label>
                    @endforeach
                    </div>
                </details>
            </div>

            <div class="catalog-filters__field">
                <label for="servicio-precio-min">Precio mínimo (€)</label>
                <input class="form-control" id="servicio-precio-min" type="number" name="precio_min" min="0" step="0.01" value="{{ request('precio_min') }}" placeholder="0">
            </div>

            <div class="catalog-filters__field">
                <label for="servicio-precio-max">Precio máximo (€)</label>
                <input class="form-control" id="servicio-precio-max" type="number" name="precio_max" min="0" step="0.01" value="{{ request('precio_max') }}" placeholder="Sin límite">
            </div>

            <div class="catalog-filters__field">
                <label for="servicio-plazo-max">Plazo máximo (días)</label>
                <input class="form-control" id="servicio-plazo-max" type="number" name="plazo_max" min="0" step="1" value="{{ request('plazo_max') }}" placeholder="Ej. 7">
            </div>

            <div class="catalog-filters__field">
                <label for="servicio-revisiones-min">Revisiones mínimas</label>
                <input class="form-control" id="servicio-revisiones-min" type="number" name="revisiones_min" min="0" step="1" value="{{ request('revisiones_min') }}" placeholder="Ej. 2">
            </div>

            <div class="catalog-filters__actions">
                <button type="submit" class="btn btn--primary">Aplicar filtros</button>
                <a href="{{ route('servicio.index') }}" class="btn btn--ghost">Limpiar filtros</a>
            </div>
        </form>
    </details>

    {{-- GRID DE SERVICIOS --}}
    @if($servicios->count())
        <div class="grid grid--4">
            @foreach($servicios as $servicio)
                @php $estaGuardado = in_array($servicio->id, $guardadosIds ?? []); @endphp
                <article class="card card--clickable card--service"
                         id="servicio-{{ $servicio->id }}"
                         data-card-link="{{ route('servicio.detail', ['id' => $servicio->id]) }}"
                         role="link"
                         tabindex="0"
                         aria-label="Ver detalle de {{ $servicio->titulo_servicio }}">
                    {{-- Visual con gradiente por tipo --}}
                    <div class="card__media">
                        @php
                            $gradients = [
                                'mezcla' => 'linear-gradient(135deg,#00c6ff,#0072ff)',
                                'master' => 'linear-gradient(135deg,#a900ef,#6200ea)',
                                'otro'   => 'linear-gradient(135deg,#00e676,#007a3d)',
                            ];
                            $grad = $gradients[$servicio->tipo_servicio] ?? 'linear-gradient(135deg,#444,#222)';
                        @endphp
                        <div style="background:{{ $grad }};
                                    display:flex;align-items:center;justify-content:center;position:relative;">
                            {{-- Icono según tipo --}}
                            @if($servicio->tipo_servicio === 'mezcla')
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5">
                                    <path d="M3 6h18M3 12h18M3 18h18"/><circle cx="7" cy="6" r="2" fill="rgba(255,255,255,0.6)"/>
                                    <circle cx="17" cy="12" r="2" fill="rgba(255,255,255,0.6)"/><circle cx="10" cy="18" r="2" fill="rgba(255,255,255,0.6)"/>
                                </svg>
                            @elseif($servicio->tipo_servicio === 'master')
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                                </svg>
                            @else
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5">
                                    <path d="M9 19V6l12-3v13"/><circle cx="6" cy="19" r="3"/><circle cx="18" cy="16" r="3"/>
                                </svg>
                            @endif

                            {{-- Badge tipo --}}
                            <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.5);
                                         backdrop-filter:blur(4px);color:#fff;font-size:10px;font-weight:700;
                                         text-transform:uppercase;letter-spacing:.6px;padding:3px 8px;border-radius:20px;">
                                {{ ucfirst($servicio->tipo_servicio) }}
                            </span>

                            {{-- Botón guardar flotante (esquina inferior derecha del media) --}}
                            @include('partials.btn-guardado', [
                                'tipo'    => 'servicio',
                                'id'      => $servicio->id,
                                'guardado'=> $estaGuardado,
                                'compact' => true,
                            ])
                        </div>
                    </div>

                    <div class="card__body">
                        <h3 class="card__title">{{ $servicio->titulo_servicio }}</h3>

                        {{-- Info del Ingeniero --}}
                        @if($servicio->usuario)
                            <div style="display:flex;align-items:center;gap:8px;margin:6px 0 10px;">
                                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#A900EF,#D26BFF);
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:11px;font-weight:800;color:#0b0b0f;flex-shrink:0;">
                                    {{ strtoupper(substr($servicio->usuario->nombre_usuario, 0, 1)) }}
                                </div>
                                <span style="font-size:12px;color:rgba(255,255,255,.6);">
                                    {{ $servicio->usuario->nombre_usuario }}
                                </span>
                            </div>
                        @endif

                        @if($servicio->descripcion_servicio)
                            <p style="font-size:12px;color:rgba(255,255,255,.5);line-height:1.5;
                                      display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $servicio->descripcion_servicio }}
                            </p>
                        @endif

                        <div class="card__foot" style="margin-top:auto;">
                            <span class="price">{{ number_format($servicio->precio_servicio, 0) }} €</span>

                            @if($servicio->plazo_entrega_dias)
                                <span style="font-size:11px;color:rgba(255,255,255,.4);">
                                    {{ $servicio->plazo_entrega_dias }}d entrega
                                </span>
                            @endif

                            <div class="card__actions">
                                <a class="btn btn--ghost" href="{{ route('servicio.detail', ['id' => $servicio->id]) }}"
                                   id="btn-ver-servicio-{{ $servicio->id }}">
                                    Ver servicio
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div style="margin-top:18px;">
            {{ $servicios->links('pagination::bootstrap-5') }}
        </div>

    @else
        <div class="panel" style="text-align:center;padding:60px 30px;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1.2" style="margin-bottom:16px;">
                <path d="M9 19V6l12-3v13"/><circle cx="6" cy="19" r="3"/><circle cx="18" cy="16" r="3"/>
            </svg>
            <p style="color:rgba(255,255,255,0.45);margin:0;font-size:15px;">
                No hay servicios disponibles con los filtros seleccionados.
            </p>
            @if($filtrosActivos)
                <a href="{{ route('servicio.index') }}" class="btn btn--ghost" style="margin-top:16px;">
                    Ver todos los servicios
                </a>
            @endif
        </div>
    @endif

@endsection
