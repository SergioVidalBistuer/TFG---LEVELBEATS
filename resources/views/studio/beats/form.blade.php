@extends('layouts.master')
@section('title', isset($beat) ? 'Editar Beat' : 'Nuevo Beat')

@section('content')
<div class="studio-page studio-page--form studio-beat-form-page">
    <div class="studio-form-head studio-beat-form-head">
        <a class="btn btn--ghost" href="{{ route('studio.beats.index') }}">Volver</a>
        <div>
            <p class="studio-eyebrow">Studio · Beats</p>
            <h1>{{ isset($beat) ? 'Editar beat' : 'Crear beat' }}</h1>
            <p class="muted">Define los datos principales del beat y su visibilidad en el marketplace.</p>
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

    <section class="studio-form-panel studio-beat-form-panel">
        <form class="studio-form studio-beat-form" method="POST" action="{{ isset($beat) ? route('studio.beats.update') : route('studio.beats.save') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($beat))
                <input type="hidden" name="id" value="{{ $beat->id }}">
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <div class="studio-field">
                        <label for="titulo_beat">Título del beat <span>*</span></label>
                        <input id="titulo_beat" type="text" name="titulo_beat" value="{{ old('titulo_beat', $beat->titulo_beat ?? '') }}" required class="form-control form-lb__input" placeholder="Ej. Dark Trap Instrumental">
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="studio-field">
                        <label for="genero_musical">Género</label>
                        <input id="genero_musical" type="text" name="genero_musical" value="{{ old('genero_musical', $beat->genero_musical ?? '') }}" class="form-control form-lb__input" placeholder="Trap, Drill, Lo-Fi...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="studio-field">
                        <label for="tono_musical">Tono</label>
                        <select id="tono_musical" name="tono_musical" class="form-select form-lb__select">
                            <option value="">Desconocido</option>
                            @foreach(['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'] as $nota)
                                <option value="{{ $nota }}" {{ (old('tono_musical', $beat->tono_musical ?? '') === $nota) ? 'selected' : '' }}>{{ $nota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="tempo_bpm">BPM</label>
                        <input id="tempo_bpm" type="number" name="tempo_bpm" value="{{ old('tempo_bpm', $beat->tempo_bpm ?? '') }}" class="form-control form-lb__input" placeholder="120">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="precio_base_licencia">Precio base de licencia (€) <span>*</span></label>
                        <input id="precio_base_licencia" type="number" step="0.01" name="precio_base_licencia" value="{{ old('precio_base_licencia', $beat->precio_base_licencia ?? '19.99') }}" required class="form-control form-lb__input" placeholder="19.99">
                        <small>Precio mínimo mostrado en el marketplace.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="studio-switch">
                        <input type="checkbox" name="activo_publicado" {{ old('activo_publicado', $beat->activo_publicado ?? true) ? 'checked' : '' }}>
                        <span>
                            <strong>Publicar en Marketplace</strong>
                            <small>Si está desactivado, el beat queda oculto.</small>
                        </span>
                    </label>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label for="archivo_audio">Archivo de audio del beat @if(!isset($beat))<span>*</span>@endif</label>
                        <input
                            id="archivo_audio"
                            type="file"
                            name="archivo_audio"
                            class="form-control form-lb__input project-file-input studio-beat-audio-input"
                            accept="audio/*,.mp3,.wav,.aiff,.aif,.flac,.m4a"
                            @if(!isset($beat)) required @endif
                        >
                        <small>
                            @if(isset($beat) && $beat->url_archivo_final)
                                Archivo actual: {{ basename($beat->url_archivo_final) }}. Sube otro archivo solo si quieres reemplazarlo.
                            @else
                                Formatos admitidos: MP3, WAV, AIFF, AIF, FLAC o M4A. Máximo 100 MB.
                            @endif
                        </small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label for="portada_beat">Portada del beat</label>
                        <input
                            id="portada_beat"
                            type="file"
                            name="portada_beat"
                            class="form-control form-lb__input project-file-input studio-cover-input"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >
                        <small>
                            @if(isset($beat) && $beat->url_portada_beat)
                                Portada actual: {{ basename($beat->url_portada_beat) }}. Sube otra imagen solo si quieres reemplazarla.
                            @else
                                Formatos admitidos: JPG, PNG o WEBP. Máximo 5 MB.
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="studio-form-actions">
                <a href="{{ route('studio.beats.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">{{ isset($beat) ? 'Guardar cambios' : 'Añadir al catálogo' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
