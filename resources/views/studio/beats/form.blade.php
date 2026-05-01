@extends('layouts.master')
@section('title', isset($beat) ? 'Editar Beat' : 'Nuevo Beat')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="font-size: 24px; margin-bottom: 24px; display: flex; align-items:center; gap: 10px;">
        <a href="{{ route('studio.beats.index') }}" style="color:#fff; text-decoration:none;">←</a>
        {{ isset($beat) ? 'Editar Parametros del Beat' : '📝 Añadir Nuevo Beat' }}
    </h1>

    <div class="panel" style="padding: 32px;">
        <form method="POST" action="{{ isset($beat) ? route('studio.beats.update') : route('studio.beats.save') }}">
            @csrf
            @if(isset($beat))
                <input type="hidden" name="id" value="{{ $beat->id }}">
            @endif

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Título del Beat <span style="color:#ff5252">*</span></label>
                <input type="text" name="titulo_beat" value="{{ old('titulo_beat', $beat->titulo_beat ?? '') }}" required class="input" style="width: 100%;" placeholder="Ej. Dark Trap Instrumental">
            </div>

            <div class="d-flex gap-3" style="margin-bottom: 24px;">
                <div style="flex:1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Género</label>
                    <input type="text" name="genero_musical" value="{{ old('genero_musical', $beat->genero_musical ?? '') }}" class="input" style="width: 100%;" placeholder="Trap, Reggaeton...">
                </div>
                <div style="flex:1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Tono</label>
                    <select name="tono_musical" style="width:100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;">
                        <option value="">Desconocido</option>
                        @foreach(['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'] as $nota)
                            <option value="{{ $nota }}" {{ (old('tono_musical', $beat->tono_musical ?? '') === $nota) ? 'selected' : '' }}>{{ $nota }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">BPM</label>
                    <input type="number" name="tempo_bpm" value="{{ old('tempo_bpm', $beat->tempo_bpm ?? '') }}" class="input" style="width: 100%;" placeholder="120">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #00e676;">Precio de Licencia Base (€) <span style="color:#ff5252">*</span></label>
                <input type="number" step="0.01" name="precio_base_licencia" value="{{ old('precio_base_licencia', $beat->precio_base_licencia ?? '19.99') }}" required class="input" style="width: 100%; border-color: #00e676;">
                <span style="font-size: 12px; color:rgba(255,255,255,.5); margin-top:6px; display:block;">Este es el precio mínimo desde el que se publicitará en el escaparate.</span>
            </div>

            <div style="margin-bottom: 32px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 4px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin:0;">
                    <input type="checkbox" name="activo_publicado" {{ old('activo_publicado', $beat->activo_publicado ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">Hacer PÚBLICO en el Marketplace</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('studio.beats.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary" style="padding: 12px 32px;">{{ isset($beat) ? 'Guardar Cambios' : 'Añadir al Catálogo' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
