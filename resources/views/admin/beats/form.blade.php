@extends('layouts.master')

@section('title', 'Admin - Editar Beat')

@section('content')
<div class="admin-page admin-beat-form-page">
    <a href="{{ route('admin.beats.index') }}" class="admin-back-link">← Volver a Beats</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Editar beat</h1>
            <p>Actualiza los datos del beat, su visibilidad y sus archivos principales.</p>
        </div>
        <span class="admin-badge">{{ $beat->usuario->nombre_usuario ?? 'Productor no disponible' }}</span>
    </header>

    @if ($errors->any())
        <div class="form-lb__error studio-form__error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="studio-form-panel studio-beat-form-panel admin-beat-form-panel">
        <form class="studio-form studio-beat-form admin-beat-form" method="POST" action="{{ route('admin.beats.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $beat->id }}">

            <div class="row g-3">
                <div class="col-12">
                    <div class="studio-field">
                        <label for="titulo_beat">Título del beat <span>*</span></label>
                        <input id="titulo_beat" type="text" name="titulo_beat" value="{{ old('titulo_beat', $beat->titulo_beat) }}" required class="form-control form-lb__input" placeholder="Ej. Dark Trap Instrumental">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="genero_musical">Género</label>
                        <input id="genero_musical" type="text" name="genero_musical" value="{{ old('genero_musical', $beat->genero_musical) }}" class="form-control form-lb__input" placeholder="Trap, Drill, Lo-Fi...">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="estado_de_animo">Estado de ánimo</label>
                        <input id="estado_de_animo" type="text" name="estado_de_animo" value="{{ old('estado_de_animo', $beat->estado_de_animo) }}" class="form-control form-lb__input" placeholder="Oscuro, melódico, energético...">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="tono_musical">Tono</label>
                        <select id="tono_musical" name="tono_musical" class="form-select form-lb__select">
                            <option value="">Desconocido</option>
                            @foreach(['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'] as $nota)
                                <option value="{{ $nota }}" {{ old('tono_musical', $beat->tono_musical) === $nota ? 'selected' : '' }}>{{ $nota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="tempo_bpm">BPM</label>
                        <input id="tempo_bpm" type="number" name="tempo_bpm" value="{{ old('tempo_bpm', $beat->tempo_bpm) }}" class="form-control form-lb__input" placeholder="120">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="studio-field">
                        <label for="precio_base_licencia">Precio base (€) <span>*</span></label>
                        <input id="precio_base_licencia" type="number" step="0.01" name="precio_base_licencia" value="{{ old('precio_base_licencia', $beat->precio_base_licencia) }}" required class="form-control form-lb__input" placeholder="19.99">
                    </div>
                </div>

                <div class="col-12">
                    <label class="studio-switch studio-switch--compact">
                        <input type="checkbox" name="activo_publicado" {{ old('activo_publicado', $beat->activo_publicado) ? 'checked' : '' }}>
                        <span>
                            <strong>Publicar en Marketplace</strong>
                            <small>Si está desactivado, el beat queda oculto en las zonas públicas.</small>
                        </span>
                    </label>
                </div>

                <div class="col-12">
                    <div class="studio-field admin-beat-file-field">
                        <label for="archivo_audio">Archivo de audio del beat</label>
                        <input
                            id="archivo_audio"
                            type="file"
                            name="archivo_audio"
                            class="form-control form-lb__input project-file-input"
                            accept="audio/*,.mp3,.wav,.aiff,.aif,.flac,.m4a"
                        >
                        <small>
                            @if($beat->url_archivo_final)
                                Archivo actual: {{ basename($beat->url_archivo_final) }}. Sube otro archivo solo si quieres reemplazarlo.
                            @else
                                No hay archivo registrado. Formatos admitidos: MP3, WAV, AIFF, AIF, FLAC o M4A. Máximo 100 MB.
                            @endif
                        </small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="studio-field admin-beat-file-field">
                        <label for="portada_beat">Portada del beat</label>
                        <input
                            id="portada_beat"
                            type="file"
                            name="portada_beat"
                            class="form-control form-lb__input project-file-input"
                            accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                        >
                        <small>
                            @if($beat->url_portada_beat)
                                Portada actual: {{ basename($beat->url_portada_beat) }}. Sube otra imagen solo si quieres reemplazarla.
                            @else
                                No hay portada registrada. Formatos admitidos: JPG, PNG o WEBP. Máximo 5 MB.
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="studio-form-actions">
                <a href="{{ route('admin.beats.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">Guardar cambios</button>
            </div>
        </form>
    </section>
</div>
@endsection
