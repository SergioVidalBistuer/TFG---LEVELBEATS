@extends('layouts.master')

@section('title', isset($coleccion) ? 'Editar Colección' : 'Crear Colección')

@section('content')
    <div class="form-lb">
        <h1>{{ isset($coleccion) ? 'Editar Colección' : 'Crear Colección' }}</h1>

        @if ($errors->any())
            <div class="form-lb__error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($coleccion)
            ? action([App\Http\Controllers\ColeccionController::class, 'update'])
            : action([App\Http\Controllers\ColeccionController::class, 'save']) }}"
              method="POST">
            @csrf

            @if(isset($coleccion))
                <input type="hidden" name="id" value="{{ $coleccion->id }}">
            @endif



            <div class="form-lb__group">
                <label for="titulo_coleccion">Título de la colección</label>
                <input id="titulo_coleccion" class="form-lb__input" type="text" name="titulo_coleccion" maxlength="140" required
                       value="{{ old('titulo_coleccion', isset($coleccion) ? $coleccion->titulo_coleccion : '') }}" placeholder="Nombre de la colección">
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="tipo_coleccion">Tipo</label>
                    <input id="tipo_coleccion" class="form-lb__input" type="text" name="tipo_coleccion" required
                           value="{{ old('tipo_coleccion', isset($coleccion) ? $coleccion->tipo_coleccion : '') }}" placeholder="publica, privada...">
                </div>

                <div class="form-lb__group">
                    <label for="estilo_genero">Género / Estilo</label>
                    <input id="estilo_genero" class="form-lb__input" type="text" name="estilo_genero"
                           value="{{ old('estilo_genero', isset($coleccion) ? $coleccion->estilo_genero : '') }}" placeholder="Trap, Drill, Lo-Fi...">
                </div>
            </div>

            <div class="form-lb__group">
                <label for="descripcion_coleccion">Descripción</label>
                <textarea id="descripcion_coleccion" class="form-lb__textarea" name="descripcion_coleccion" placeholder="Describe tu colección...">{{ old('descripcion_coleccion', isset($coleccion) ? $coleccion->descripcion_coleccion : '') }}</textarea>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="precio">Precio (€)</label>
                    <input id="precio" class="form-lb__input" type="text" inputmode="decimal" name="precio"
                           value="{{ old('precio', isset($coleccion) ? $coleccion->precio : '') }}" placeholder="29.99">
                </div>

                <div class="form-lb__group" style="display:flex; align-items:flex-end;">
                    <label class="form-lb__check">
                        <input type="checkbox" name="es_destacada" value="1"
                            {{ old('es_destacada', isset($coleccion) ? (int)$coleccion->es_destacada : 0) ? 'checked' : '' }}>
                        Destacada
                    </label>
                </div>
            </div>

            {{-- Selección de Beats --}}
            <div class="form-lb__group">
                <label>Beats de la colección</label>
                <div class="panel panel--dark" style="max-height: 250px; overflow-y: auto; padding: 12px;">
                    @php
                        $selectedBeats = isset($coleccion) ? $coleccion->beats->pluck('id')->toArray() : (old('beats') ?? []);
                    @endphp
                    @foreach($beats as $beat)
                        <label class="form-lb__check" style="margin-bottom: 8px;">
                            <input type="checkbox" name="beats[]" value="{{ $beat->id }}"
                                {{ in_array($beat->id, $selectedBeats) ? 'checked' : '' }}>
                            {{ $beat->titulo_beat }} <span style="color: rgba(255,255,255,.4); font-size: 12px;">— {{ $beat->genero_musical ?? 'Sin género' }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary" type="submit">{{ isset($coleccion) ? 'Actualizar' : 'Guardar' }}</button>
                <a class="btn btn--ghost" href="{{ route('coleccion.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
