@extends('layouts.master')

@section('title', isset($coleccion) ? 'Editar Colección' : 'Crear Colección')

@section('content')
@php
    $studioMode = $studioMode ?? false;
    $formAction = isset($coleccion)
        ? ($studioMode ? route('studio.colecciones.update') : route('coleccion.update'))
        : ($studioMode ? route('studio.colecciones.save') : route('coleccion.save'));
    $cancelRoute = $studioMode ? route('studio.colecciones.index') : route('coleccion.index');
    $selectedBeats = isset($coleccion) ? $coleccion->beats->pluck('id')->toArray() : (old('beats') ?? []);
@endphp

<div class="studio-page studio-page--form">
    <div class="studio-form-head">
        <a class="btn btn--ghost" href="{{ $cancelRoute }}">Volver</a>
        <div>
            <p class="studio-eyebrow">{{ $studioMode ? 'Studio · Colecciones' : 'Colecciones' }}</p>
            <h1>{{ isset($coleccion) ? 'Editar colección' : 'Crear colección' }}</h1>
            <p class="muted">Agrupa beats, define el precio del pack y configura su visibilidad destacada.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="form-lb__error studio-form__error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="studio-form-panel">
        <form class="studio-form" action="{{ $formAction }}" method="POST">
            @csrf

            @if(isset($coleccion))
                <input type="hidden" name="id" value="{{ $coleccion->id }}">
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <div class="studio-field">
                        <label for="titulo_coleccion">Título de la colección <span>*</span></label>
                        <input id="titulo_coleccion" class="form-control form-lb__input" type="text" name="titulo_coleccion" maxlength="140" required
                               value="{{ old('titulo_coleccion', isset($coleccion) ? $coleccion->titulo_coleccion : '') }}" placeholder="Nombre de la colección">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="tipo_coleccion">Tipo <span>*</span></label>
                        <input id="tipo_coleccion" class="form-control form-lb__input" type="text" name="tipo_coleccion" required
                               value="{{ old('tipo_coleccion', isset($coleccion) ? $coleccion->tipo_coleccion : '') }}" placeholder="Pack, selección, temporada...">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="estilo_genero">Género / estilo</label>
                        <input id="estilo_genero" class="form-control form-lb__input" type="text" name="estilo_genero"
                               value="{{ old('estilo_genero', isset($coleccion) ? $coleccion->estilo_genero : '') }}" placeholder="Trap, Drill, Lo-Fi...">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="precio">Precio (€)</label>
                        <input id="precio" class="form-control form-lb__input" type="number" name="precio" min="0" step="0.01"
                               value="{{ old('precio', isset($coleccion) ? $coleccion->precio : 0) }}" placeholder="0.00">
                    </div>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label for="descripcion_coleccion">Descripción</label>
                        <textarea id="descripcion_coleccion" class="form-control form-lb__textarea" name="descripcion_coleccion" placeholder="Describe el concepto de la colección...">{{ old('descripcion_coleccion', isset($coleccion) ? $coleccion->descripcion_coleccion : '') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <label class="studio-switch studio-switch--compact">
                        <input type="checkbox" name="es_destacada" value="1"
                            {{ old('es_destacada', isset($coleccion) ? (int)$coleccion->es_destacada : 0) ? 'checked' : '' }}>
                        <span>
                            <strong>Marcar como destacada</strong>
                            <small>Úsalo para resaltar colecciones importantes en el catálogo.</small>
                        </span>
                    </label>
                </div>

                <div class="col-12">
                    <label class="studio-switch studio-switch--compact">
                        <input type="checkbox" name="activo_publicado" value="1"
                            {{ old('activo_publicado', isset($coleccion) ? (int)$coleccion->activo_publicado : 1) ? 'checked' : '' }}>
                        <span>
                            <strong>Visible en Marketplace</strong>
                            <small>Si lo desactivas, la colección queda oculta del catálogo público y del buscador.</small>
                        </span>
                    </label>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label>Beats de la colección</label>
                        <div class="studio-beat-picker">
                            @forelse($beats as $beat)
                                <label class="studio-beat-option">
                                    <input type="checkbox" name="beats[]" value="{{ $beat->id }}"
                                        {{ in_array($beat->id, $selectedBeats) ? 'checked' : '' }}>
                                    <span>
                                        <strong>{{ $beat->titulo_beat }}</strong>
                                        <small>{{ $beat->genero_musical ?? 'Sin género' }}</small>
                                    </span>
                                </label>
                            @empty
                                <div class="studio-empty studio-empty--compact">
                                    <h2>No tienes beats disponibles</h2>
                                    <p class="muted">Primero sube beats a Studio para poder añadirlos a una colección.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="studio-form-actions">
                <a class="btn btn--ghost" href="{{ $cancelRoute }}">Cancelar</a>
                <button class="btn btn--primary" type="submit">{{ isset($coleccion) ? 'Guardar cambios' : 'Crear colección' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
