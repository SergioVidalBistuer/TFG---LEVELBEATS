@extends('layouts.master')

@section('title', isset($servicio) ? 'Admin — Editar Servicio' : 'Admin — Nuevo Servicio')

@section('content')
<style>
    .admin-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: rgba(255,255,255,0.4);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 20px;
        transition: color .15s;
    }
    .admin-back:hover { color: #D26BFF; }

    .form-panel {
        max-width: 660px;
        margin: 0 auto;
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 36px;
    }
    .form-title {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 28px;
        letter-spacing: -0.3px;
    }
    .field { margin-bottom: 22px; }
    .field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: rgba(255,255,255,0.45);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
    }
    .field label.accent { color: #D26BFF; }
    .field-required { color: rgba(255,100,100,0.8); margin-left: 2px; }
    .field input[type="text"],
    .field input[type="number"],
    .field input[type="url"],
    .field select,
    .field textarea {
        width: 100%;
        padding: 11px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color .2s, background .2s, box-shadow .2s;
        box-sizing: border-box;
    }
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: rgba(169,0,239,0.5);
        background: rgba(169,0,239,0.05);
        box-shadow: 0 0 0 3px rgba(169,0,239,0.10);
    }
    .field input.accent-border { border-color: rgba(169,0,239,0.3); }
    .field select option { background: #12121a; }
    .field textarea { resize: vertical; min-height: 90px; }
    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 10px;
        cursor: pointer;
        margin-bottom: 28px;
    }
    .checkbox-row input[type="checkbox"] {
        width: 18px; height: 18px;
        accent-color: #A900EF;
        flex-shrink: 0;
        cursor: pointer;
    }
    .checkbox-row span { font-size: 14px; font-weight: 600; color: rgba(255,255,255,0.8); }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn-cancel {
        padding: 10px 20px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: rgba(255,255,255,0.5);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: border-color .15s, color .15s;
    }
    .btn-cancel:hover { border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.8); }
    .btn-submit {
        padding: 10px 26px;
        background: #A900EF;
        border: 1px solid rgba(169,0,239,0.5);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s, transform .1s;
        box-shadow: 0 6px 20px rgba(169,0,239,0.25);
    }
    .btn-submit:hover { background: #c200ff; transform: translateY(-1px); }
    .field-hint {
        font-size: 12px;
        color: rgba(255,255,255,0.25);
        margin-top: 5px;
    }
    .no-engineers {
        font-size: 12px;
        color: rgba(255,150,100,0.7);
        margin-top: 5px;
    }
    .divider {
        height: 1px;
        background: rgba(255,255,255,0.06);
        margin: 24px 0;
    }
</style>

<div style="max-width: 700px; margin: 0 auto;">
    <a href="{{ route('admin.servicios.index') }}" class="admin-back">← Volver a Servicios B2B</a>

    <div class="form-panel">
        <h1 class="form-title">
            {{ isset($servicio) ? '✏️ Editar Servicio' : '📝 Nuevo Servicio' }}
        </h1>

        <form method="POST" action="{{ isset($servicio) ? route('admin.servicios.update') : route('admin.servicios.save') }}">
            @csrf
            @if(isset($servicio))
                <input type="hidden" name="id" value="{{ $servicio->id }}">
            @endif

            {{-- Propietario --}}
            <div class="field">
                <label class="accent">Ingeniero Propietario <span class="field-required">*</span></label>
                <select name="id_usuario" required>
                    <option value="">Selecciona un ingeniero...</option>
                    @foreach($ingenieros as $ing)
                        <option value="{{ $ing->id }}"
                            {{ old('id_usuario', $servicio->id_usuario ?? '') == $ing->id ? 'selected' : '' }}>
                            {{ $ing->nombre_usuario }} — {{ $ing->direccion_correo }}
                        </option>
                    @endforeach
                </select>
                @if($ingenieros->isEmpty())
                    <p class="no-engineers">⚠ No hay usuarios con rol <strong>ingeniero</strong> activo en la plataforma.</p>
                @endif
            </div>

            <div class="divider"></div>

            {{-- Título --}}
            <div class="field">
                <label>Título del Servicio <span class="field-required">*</span></label>
                <input type="text" name="titulo_servicio" required
                       value="{{ old('titulo_servicio', $servicio->titulo_servicio ?? '') }}"
                       placeholder="Ej. Masterización Premium [Stem Mastering]">
            </div>

            {{-- Tipo --}}
            <div class="field">
                <label>Categoría Técnica</label>
                <select name="tipo_servicio">
                    <option value="">Selecciona un tipo...</option>
                    @php $tiposEnum = ['mezcla' => 'Mezcla (Mixing)', 'master' => 'Masterización (Mastering)', 'otro' => 'Producción / Otros']; @endphp
                    @foreach($tiposEnum as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('tipo_servicio', $servicio->tipo_servicio ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Plazo + Revisiones --}}
            <div class="field-row" style="margin-bottom: 22px;">
                <div class="field" style="margin-bottom:0;">
                    <label>Plazo máximo (Días) <span class="field-required">*</span></label>
                    <input type="number" name="plazo_entrega_dias" required min="1"
                           value="{{ old('plazo_entrega_dias', $servicio->plazo_entrega_dias ?? '5') }}"
                           placeholder="Ej. 7">
                </div>
                <div class="field" style="margin-bottom:0;">
                    <label>Nº de Revisiones</label>
                    <input type="number" name="numero_revisiones" min="0"
                           value="{{ old('numero_revisiones', $servicio->numero_revisiones ?? '3') }}"
                           placeholder="Ej. 3">
                </div>
            </div>

            {{-- Precio --}}
            <div class="field">
                <label class="accent">Precio del Pack (€) <span class="field-required">*</span></label>
                <input type="number" step="0.01" name="precio_servicio" required min="0"
                       class="accent-border"
                       value="{{ old('precio_servicio', $servicio->precio_servicio ?? '90.00') }}">
            </div>

            {{-- Portafolio --}}
            <div class="field">
                <label>Enlace Portafolio / Ejemplos de Audio (URL)</label>
                <input type="url" name="url_portafolio"
                       value="{{ old('url_portafolio', $servicio->url_portafolio ?? '') }}"
                       placeholder="https://soundcloud.com/...">
            </div>

            {{-- Descripción --}}
            <div class="field">
                <label>Desglose del contrato / trabajo</label>
                <textarea name="descripcion_servicio"
                          placeholder="Detalla qué incluye el servicio (número máximo de stems, etc)...">{{ old('descripcion_servicio', $servicio->descripcion_servicio ?? '') }}</textarea>
            </div>

            {{-- Activo --}}
            <label class="checkbox-row">
                <input type="checkbox" name="servicio_activo"
                       {{ old('servicio_activo', $servicio->servicio_activo ?? true) ? 'checked' : '' }}>
                <span>Servicio ACTIVO (Aceptar contrataciones)</span>
            </label>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('admin.servicios.index') }}" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-submit">
                    {{ isset($servicio) ? 'Guardar Cambios' : 'Crear Servicio' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
