@extends('layouts.master')

@section('title', $coleccion->titulo_coleccion)

@section('content')
    <div class="row" style="margin-top: 20px;">
        {{-- COLUMNA IZQUIERDA: Info --}}
        <div class="col-md-7">
            <h1 style="margin-bottom: 6px;">{{ $coleccion->titulo_coleccion }}</h1>

            <p class="card__meta">Tipo: {{ $coleccion->tipo_coleccion ?? 'No especificado' }}</p>
            <p class="card__meta">Género: {{ $coleccion->estilo_genero ?? 'No especificado' }}</p>

            @if($coleccion->descripcion_coleccion)
                <div class="panel panel--dark" style="margin-top:14px;">
                    <h3 style="margin-top:0;">Descripción</h3>
                    <p>{{ $coleccion->descripcion_coleccion }}</p>
                </div>
            @endif

            <p class="price" style="font-size: 24px; margin-top: 16px;">{{ $coleccion->precio ?? '—' }} €</p>

            {{-- Añadir colección al carrito --}}
            <div style="margin-top: auto;">
                @if(session()->has('usuario_id'))
                    <form action="{{ route('carrito.addColeccion') }}" method="POST" class="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $coleccion->id }}">
                        <div class="input-group-custom">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="99" class="input--lb">
                        </div>
                        <button class="btn btn--primary" type="submit">Añadir colección al carrito</button>
                    </form>
                @else
                    <p style="margin-top: 20px;">
                        <a class="btn btn--ghost" href="{{ route('login') }}">
                            Inicia sesión para comprar
                        </a>
                    </p>
                @endif
            </div>
        </div>

        {{-- COLUMNA DERECHA: Portada --}}
        <div class="col-md-5">
            @if($coleccion->beats->first() && $coleccion->beats->first()->url_portada_beat)
                <img src="{{ asset($coleccion->beats->first()->url_portada_beat) }}"
                     alt="Portada {{ $coleccion->titulo_coleccion }}"
                     style="width:100%;border-radius:var(--radius-sm);box-shadow: 0 10px 30px rgba(0,0,0,.4);">
            @else
                <div style="width:100%;height:300px;background:linear-gradient(135deg, var(--primary), #1a1a2e);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                </div>
            @endif
        </div>
    </div>

    {{-- PROPIETARIO O ADMIN --}}
    @if(session('rol') === 'admin' || session('usuario_id') === $coleccion->id_usuario)
        <div class="admin-actions d-flex gap-2" style="margin-top: 15px;">
            <a class="btn btn--ghost" href="{{ route('coleccion.edit', $coleccion->id) }}">Editar</a>
            <a class="btn btn--ghost" style="color: #ff4d4d; border-color: rgba(255,77,77,.3);" href="{{ route('coleccion.delete', $coleccion->id) }}"
               onclick="return confirm('¿Seguro que quieres eliminar esta colección?')">
                Eliminar
            </a>
        </div>
    @endif

    {{-- BEATS DE LA COLECCIÓN --}}
    <div style="margin-top: 40px;">
        <h2>Beats de esta colección</h2>

        @if($coleccion->beats->count())
            <div class="grid grid--4">
                @foreach($coleccion->beats as $beat)
                    <article class="card">
                        <div class="card__media">
                            <img src="{{ asset($beat->url_portada_beat) }}"
                                 alt="Portada {{ $beat->titulo_beat }}"
                                 style="width:100%;height:128px;object-fit:cover;">
                        </div>

                        <div class="card__body">
                            <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                            <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>

                            <div class="card__foot">
                                <span class="price">{{ $beat->precio_base_licencia }} €</span>

                                <div class="card__actions">
                                    <a class="btn btn--ghost" href="{{ route('beat.detail', ['id' => $beat->id]) }}">Ver</a>
                                </div>
                            </div>

                            <div style="margin-top: auto;">
                                @if(session()->has('usuario_id'))
                                    <form action="{{ route('carrito.addBeat') }}" method="POST" style="margin-top:12px;display:flex;gap:10px;align-items:center;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $beat->id }}">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button class="btn btn--primary w-100" type="submit">Añadir al carrito</button>
                                    </form>
                                @else
                                    <p style="margin-bottom: 0; padding-top: 12px; text-align: center;">
                                        <a href="{{ route('login') }}" class="btn btn--ghost w-100" style="color: var(--white-soft);">
                                            Inicia sesión para comprar
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="panel" style="text-align: center; padding: 30px;">
                <p style="color: rgba(255,255,255,0.6);">Esta colección aún no tiene beats asociados.</p>
            </div>
        @endif
    </div>

    <div style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('coleccion.index') }}">Volver a Colecciones</a>
    </div>
@endsection
