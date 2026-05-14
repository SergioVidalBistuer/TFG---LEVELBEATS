@extends('layouts.master')

@section('title', 'Listado de Beats')
@section('hero')
    <section class="lb-hero lb-hero--beats">
        <div class="lb-hero__inner container">
            <h1 class="lb-hero__title">Beats</h1>
        </div>
    </section>
@endsection
@section('content')
    @php
        $filtrosActivos = collect(request()->only(['q', 'genero', 'bpm_min', 'bpm_max', 'tono', 'estado', 'precio_min', 'precio_max']))
            ->filter(fn($value) => filled($value))
            ->isNotEmpty();
    @endphp

    <details class="catalog-filters" {{ $filtrosActivos ? 'open' : '' }}>
        <summary class="catalog-filters__summary">
            <span>Filtros</span>
            <span class="catalog-filters__chevron">⌄</span>
        </summary>

        <form class="catalog-filters__form" method="GET" action="{{ route('beat.index') }}">
            <div class="catalog-filters__field catalog-filters__field--wide">
                <label for="beat-q">Buscar por título</label>
                <input class="form-control" id="beat-q" type="search" name="q" value="{{ request('q') }}" placeholder="Ej. trap oscuro">
            </div>

            <div class="catalog-filters__field">
                <label>Género musical</label>
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
                <label for="beat-bpm-min">BPM mínimo</label>
                <input class="form-control" id="beat-bpm-min" type="number" name="bpm_min" min="0" step="1" value="{{ request('bpm_min') }}" placeholder="Ej. 80">
            </div>

            <div class="catalog-filters__field">
                <label for="beat-bpm-max">BPM máximo</label>
                <input class="form-control" id="beat-bpm-max" type="number" name="bpm_max" min="0" step="1" value="{{ request('bpm_max') }}" placeholder="Ej. 160">
            </div>

            <div class="catalog-filters__field">
                <label>Tono musical</label>
                <details class="catalog-filter-dropdown">
                    <summary>
                        <span>{{ request('tono') ?: 'Todos' }}</span>
                        <span class="catalog-filter-dropdown__chevron"></span>
                    </summary>
                    <div class="catalog-filter-dropdown__menu">
                        <label class="catalog-filter-dropdown__item {{ request('tono') ? '' : 'is-active' }}">
                            <input type="radio" name="tono" value="" {{ request('tono') ? '' : 'checked' }}>
                            Todos
                        </label>
                    @foreach($opcionesFiltro['tonos'] as $tono)
                        <label class="catalog-filter-dropdown__item {{ request('tono') === $tono ? 'is-active' : '' }}">
                            <input type="radio" name="tono" value="{{ $tono }}" {{ request('tono') === $tono ? 'checked' : '' }}>
                            {{ $tono }}
                        </label>
                    @endforeach
                    </div>
                </details>
            </div>

            <div class="catalog-filters__field">
                <label>Estado de ánimo</label>
                <details class="catalog-filter-dropdown">
                    <summary>
                        <span>{{ request('estado') ?: 'Todos' }}</span>
                        <span class="catalog-filter-dropdown__chevron"></span>
                    </summary>
                    <div class="catalog-filter-dropdown__menu">
                        <label class="catalog-filter-dropdown__item {{ request('estado') ? '' : 'is-active' }}">
                            <input type="radio" name="estado" value="" {{ request('estado') ? '' : 'checked' }}>
                            Todos
                        </label>
                    @foreach($opcionesFiltro['estados'] as $estado)
                        <label class="catalog-filter-dropdown__item {{ request('estado') === $estado ? 'is-active' : '' }}">
                            <input type="radio" name="estado" value="{{ $estado }}" {{ request('estado') === $estado ? 'checked' : '' }}>
                            {{ $estado }}
                        </label>
                    @endforeach
                    </div>
                </details>
            </div>

            <div class="catalog-filters__field">
                <label for="beat-precio-min">Precio mínimo (€)</label>
                <input class="form-control" id="beat-precio-min" type="number" name="precio_min" min="0" step="0.01" value="{{ request('precio_min') }}" placeholder="0">
            </div>

            <div class="catalog-filters__field">
                <label for="beat-precio-max">Precio máximo (€)</label>
                <input class="form-control" id="beat-precio-max" type="number" name="precio_max" min="0" step="0.01" value="{{ request('precio_max') }}" placeholder="Sin límite">
            </div>

            <div class="catalog-filters__actions">
                <button class="btn btn--primary" type="submit">Aplicar filtros</button>
                <a class="btn btn--ghost" href="{{ route('beat.index') }}">Limpiar filtros</a>
            </div>
        </form>
    </details>

    <div class="grid grid--4">
        @forelse($beats as $beat)
            @php
                $puedeGestionarBeat = auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $beat->id_usuario);
                $estaGuardado = in_array($beat->id, $guardadosIds ?? []);
                $srcPortadaBeat = \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg');
            @endphp

            <article class="card card--clickable"
                     data-card-link="{{ route('beat.detail', ['id' => $beat->id]) }}"
                     role="link"
                     tabindex="0"
                     aria-label="Ver detalle de {{ $beat->titulo_beat }}">
                <div class="card__media">
                    <img src="{{ $srcPortadaBeat }}"
                         alt="Portada {{ $beat->titulo_beat }}"
                         width="640"
                         height="360"
                         sizes="(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 25vw"
                         loading="lazy"
                         decoding="async">
                    {{-- Botón guardar flotante en la imagen --}}
                    @include('partials.btn-guardado', [
                        'tipo'    => 'beat',
                        'id'      => $beat->id,
                        'guardado'=> $estaGuardado,
                        'compact' => true,
                    ])
                </div>

                <div class="card__body">
                    <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                    @include('partials.product-owner', [
                        'usuario' => $beat->usuario,
                        'role' => 'Productor',
                        'variant' => 'card',
                    ])
                    <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>

                    <div class="card__foot">
                        <span class="price">{{ $beat->precio_base_licencia }} €</span>

                        <div class="card__actions">
                            @if($puedeGestionarBeat)
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
                                        <a class="user-dropdown__item" href="{{ route('beat.detail', ['id' => $beat->id]) }}" role="menuitem">Ver</a>
                                        <a class="user-dropdown__item" href="{{ route('studio.beats.edit', ['id' => $beat->id]) }}" role="menuitem">Editar</a>
                                        <a class="user-dropdown__item user-dropdown__item--danger"
                                           href="{{ route('studio.beats.delete', ['id' => $beat->id]) }}"
                                           role="menuitem"
                                           onclick="return confirm('¿Seguro que quieres borrar este beat?')">
                                            Eliminar
                                        </a>
                                    </div>
                                </div>
                            @else
                                <a class="btn btn--ghost" href="{{ route('beat.detail', ['id' => $beat->id]) }}">Ver</a>
                            @endif
                        </div>
                    </div>

                    <div style="margin-top: auto;">
                        @if(auth()->check())
                            @if(!auth()->user()->esAdmin())
                                <form action="{{ route('carrito.addBeat') }}" method="POST" style="margin-top:12px;display:flex;gap:10px;align-items:center;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $beat->id }}">
                                    <button class="btn btn--primary w-100" type="submit">Añadir al carrito</button>
                                </form>
                            @endif
                        @else
                            <p style="margin-bottom: 0; padding-top: 12px; text-align: center;">
                                <a href="{{ route('login') }}" class="btn btn--ghost w-100">
                                    Inicia sesión para comprar
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="catalog-filters__empty">
                No hay beats que coincidan con los filtros seleccionados.
            </div>
        @endforelse
    </div>
    <div style="margin-top:18px;">
        {{ $beats->links('pagination::bootstrap-5') }}
    </div>
@endsection
