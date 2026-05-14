@extends('layouts.master')

@section('title', 'Listado de Colecciones')
@section('hero')
    <section class="lb-hero lb-hero--colecciones">
        <div class="lb-hero__inner container">
            <h1 class="lb-hero__title">Colecciones</h1>
        </div>
    </section>
@endsection
@section('content')
    @php
        $filtrosActivos = collect(request()->only(['q', 'tipo', 'genero', 'beats_min', 'beats_max', 'precio_min', 'precio_max']))
            ->filter(fn($value) => filled($value))
            ->isNotEmpty();
    @endphp

    <details class="catalog-filters" {{ $filtrosActivos ? 'open' : '' }}>
        <summary class="catalog-filters__summary">
            <span>Filtros</span>
            <span class="catalog-filters__chevron">⌄</span>
        </summary>

        <form class="catalog-filters__form" method="GET" action="{{ route('coleccion.index') }}">
            <div class="catalog-filters__field catalog-filters__field--wide">
                <label for="coleccion-q">Buscar por título</label>
                <input class="form-control" id="coleccion-q" type="search" name="q" value="{{ request('q') }}" placeholder="Ej. drill pack">
            </div>

            <div class="catalog-filters__field">
                <label>Tipo de colección</label>
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
                <label>Estilo / género</label>
                <details class="catalog-filter-dropdown">
                    <summary>
                        <span>{{ request('genero') ?: 'Todos' }}</span>
                        <span class="catalog-filter-dropdown__chevron"></span>
                    </summary>
                    <div class="catalog-filter-dropdown__menu">
                        <label class="catalog-filter-dropdown__item {{ request('genero') ? '' : 'is-active' }}">
                            <input type="radio" name="genero" value="" {{ request('genero') ? '' : 'checked' }}>
                            Todos
                        </label>
                    @foreach($opcionesFiltro['generos'] as $genero)
                        <label class="catalog-filter-dropdown__item {{ request('genero') === $genero ? 'is-active' : '' }}">
                            <input type="radio" name="genero" value="{{ $genero }}" {{ request('genero') === $genero ? 'checked' : '' }}>
                            {{ $genero }}
                        </label>
                    @endforeach
                    </div>
                </details>
            </div>

            <div class="catalog-filters__field">
                <label for="coleccion-beats-min">Beats mínimos</label>
                <input class="form-control" id="coleccion-beats-min" type="number" name="beats_min" min="0" step="1" value="{{ request('beats_min') }}" placeholder="Ej. 3">
            </div>

            <div class="catalog-filters__field">
                <label for="coleccion-beats-max">Beats máximos</label>
                <input class="form-control" id="coleccion-beats-max" type="number" name="beats_max" min="0" step="1" value="{{ request('beats_max') }}" placeholder="Sin límite">
            </div>

            <div class="catalog-filters__field">
                <label for="coleccion-precio-min">Precio mínimo (€)</label>
                <input class="form-control" id="coleccion-precio-min" type="number" name="precio_min" min="0" step="0.01" value="{{ request('precio_min') }}" placeholder="0">
            </div>

            <div class="catalog-filters__field">
                <label for="coleccion-precio-max">Precio máximo (€)</label>
                <input class="form-control" id="coleccion-precio-max" type="number" name="precio_max" min="0" step="0.01" value="{{ request('precio_max') }}" placeholder="Sin límite">
            </div>

            <div class="catalog-filters__actions">
                <button class="btn btn--primary" type="submit">Aplicar filtros</button>
                <a class="btn btn--ghost" href="{{ route('coleccion.index') }}">Limpiar filtros</a>
            </div>
        </form>
    </details>

    <div class="grid grid--4">
        @forelse($colecciones as $coleccion)
            @php
                $puedeGestionarColeccion = auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $coleccion->id_usuario);
                $estaGuardado = in_array($coleccion->id, $guardadosIds ?? []);
                $portadaCol = $coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg';
                $srcPortadaCol = \App\Support\Imagenes::portada($portadaCol);
            @endphp

            <article class="card card--clickable"
                     data-card-link="{{ route('coleccion.detail', ['id' => $coleccion->id]) }}"
                     role="link"
                     tabindex="0"
                     aria-label="Ver detalle de {{ $coleccion->titulo_coleccion }}">
                <div class="card__media">
                    @if($srcPortadaCol)
                        <img src="{{ $srcPortadaCol }}"
                             alt="Portada {{ $coleccion->titulo_coleccion }}"
                             width="640"
                             height="360"
                             sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 25vw"
                             loading="lazy"
                             decoding="async">
                    @else
                        <div style="background:linear-gradient(135deg, var(--primary), #1a1a2e);display:flex;align-items:center;justify-content:center;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </div>
                    @endif
                    {{-- Botón guardar flotante --}}
                    @include('partials.btn-guardado', [
                        'tipo'    => 'coleccion',
                        'id'      => $coleccion->id,
                        'guardado'=> $estaGuardado,
                        'compact' => true,
                    ])
                </div>

                <div class="card__body">
                    <h3 class="card__title">{{ $coleccion->titulo_coleccion }}</h3>
                    @include('partials.product-owner', [
                        'usuario' => $coleccion->usuario,
                        'role' => 'Productor',
                        'variant' => 'card',
                    ])
                    <p class="card__meta">Tipo: {{ $coleccion->tipo_coleccion ?? '-' }}</p>
                    <p class="card__meta">Género: {{ $coleccion->estilo_genero ?? '-' }}</p>
                    <p class="card__meta" style="font-size:12px; opacity:.6;">{{ $coleccion->beats->count() }} beats</p>

                    <div class="card__foot">
                        <span class="price">{{ number_format($coleccion->precio, 2, ',', '.') }} €</span>

                        <div class="card__actions">
                            @if($puedeGestionarColeccion)
                                <div class="user-dropdown product-actions-dropdown" data-dropdown>
                                    <button
                                        class="user-dropdown__trigger"
                                        type="button"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        Acciones
                                        <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <div class="user-dropdown__menu" role="menu">
                                        <a class="user-dropdown__item" href="{{ route('coleccion.detail', ['id' => $coleccion->id]) }}" role="menuitem">Ver</a>
                                        <a class="user-dropdown__item" href="{{ route('studio.colecciones.edit', ['id' => $coleccion->id]) }}" role="menuitem">Editar</a>
                                        <a class="user-dropdown__item user-dropdown__item--danger"
                                           href="{{ route('studio.colecciones.delete', ['id' => $coleccion->id]) }}"
                                           role="menuitem"
                                           onclick="return confirm('¿Seguro que quieres borrar esta colección?')">
                                            Eliminar
                                        </a>
                                    </div>
                                </div>
                            @else
                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', ['id' => $coleccion->id]) }}">Ver</a>
                            @endif
                        </div>
                    </div>


                </div>
            </article>
        @empty
            <div class="catalog-filters__empty">
                No hay colecciones que coincidan con los filtros seleccionados.
            </div>
        @endforelse
    </div>
    <div style="margin-top:18px;">
        {{ $colecciones->links('pagination::bootstrap-5') }}
    </div>
@endsection
