@extends('layouts.master')

@section('title', isset($beat) ? 'Editar Beat' : 'Crear Beat')

@section('content')
    <div class="form-lb">
        <h1>{{ isset($beat) ? 'Editar Beat' : 'Crear Beat' }}</h1>

        @if ($errors->any())
            <div class="form-lb__error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($beat)
            ? action([App\Http\Controllers\BeatController::class, 'update'])
            : action([App\Http\Controllers\BeatController::class, 'save']) }}"
              method="POST">
            @csrf

            @if(isset($beat))
                <input type="hidden" name="id" value="{{ $beat->id }}">
            @endif

            <div class="form-lb__group">
                <label for="titulo_beat">Título</label>
                <input id="titulo_beat" class="form-lb__input" type="text" name="titulo_beat" maxlength="140" required
                       value="{{ old('titulo_beat', isset($beat) ? $beat->titulo_beat : '') }}" placeholder="Nombre del beat">
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="genero_musical">Género</label>
                    <input id="genero_musical" class="form-lb__input" type="text" name="genero_musical" maxlength="80"
                           value="{{ old('genero_musical', isset($beat) ? $beat->genero_musical : '') }}" placeholder="Trap, Drill, Lo-Fi...">
                </div>

                <div class="form-lb__group">
                    <label for="tempo_bpm">Tempo (BPM)</label>
                    <input id="tempo_bpm" class="form-lb__input" type="number" name="tempo_bpm"
                           value="{{ old('tempo_bpm', isset($beat) ? $beat->tempo_bpm : '') }}" placeholder="140">
                </div>
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label for="tono_musical">Tono musical</label>
                    @php
                        $tonos = ['','C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
                        $tonoSel = old('tono_musical', isset($beat) ? $beat->tono_musical : '');
                    @endphp
                    <select id="tono_musical" class="form-lb__select" name="tono_musical">
                        @foreach($tonos as $tono)
                            <option value="{{ $tono }}" {{ $tono === $tonoSel ? 'selected' : '' }}>
                                {{ $tono === '' ? 'Seleccionar...' : $tono }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-lb__group">
                    <label for="estado_de_animo">Estado de ánimo</label>
                    <input id="estado_de_animo" class="form-lb__input" type="text" name="estado_de_animo" maxlength="80"
                           value="{{ old('estado_de_animo', isset($beat) ? $beat->estado_de_animo : '') }}" placeholder="Dark, Chill, Energético...">
                </div>
            </div>

            <div class="form-lb__group">
                <label for="precio_base_licencia">Precio (€)</label>
                <input id="precio_base_licencia" class="form-lb__input" type="number" step="0.01" name="precio_base_licencia"
                       value="{{ old('precio_base_licencia', isset($beat) ? $beat->precio_base_licencia : 0) }}" placeholder="9.99">
            </div>

            <div class="form-lb__group">
                <label for="url_audio_previsualizacion">URL audio previsualización</label>
                <input id="url_audio_previsualizacion" class="form-lb__input" type="text" name="url_audio_previsualizacion" maxlength="255"
                       value="{{ old('url_audio_previsualizacion', isset($beat) ? $beat->url_audio_previsualizacion : '') }}" placeholder="media/audio/beat.mp3">
            </div>

            <div class="form-lb__group">
                <label for="url_archivo_final">URL archivo final</label>
                <input id="url_archivo_final" class="form-lb__input" type="text" name="url_archivo_final" maxlength="255"
                       value="{{ old('url_archivo_final', isset($beat) ? $beat->url_archivo_final : '') }}" placeholder="media/audio/beat_final.wav">
            </div>

            <div class="form-lb__group">
                <label for="url_portada_beat">URL portada</label>
                <input id="url_portada_beat" class="form-lb__input" type="text" name="url_portada_beat" maxlength="255"
                       value="{{ old('url_portada_beat', isset($beat) ? $beat->url_portada_beat : '') }}" placeholder="media/img/portada.jpg">
            </div>

            <div class="form-lb__row">
                <div class="form-lb__group">
                    <label class="form-lb__check">
                        <input type="checkbox" name="activo_publicado" value="1"
                            {{ old('activo_publicado', isset($beat) ? (int)$beat->activo_publicado : 0) ? 'checked' : '' }}>
                        Publicado
                    </label>
                </div>

                <div class="form-lb__group">
                    <label for="fecha_publicacion">Fecha publicación</label>
                    <input id="fecha_publicacion" class="form-lb__input" type="datetime-local" name="fecha_publicacion"
                           value="{{ old('fecha_publicacion', (isset($beat) && $beat->fecha_publicacion) ? $beat->fecha_publicacion->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>

            <div class="form-lb__actions">
                <button class="btn btn--primary" type="submit">{{ isset($beat) ? 'Actualizar' : 'Guardar' }}</button>
                <a class="btn btn--ghost" href="{{ route('beat.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
