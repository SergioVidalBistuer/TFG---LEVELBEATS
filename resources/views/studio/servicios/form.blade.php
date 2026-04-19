@extends('layouts.master')
@section('title', isset($servicio) ? 'Editar Servicio' : 'Nuevo Servicio')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="font-size: 24px; margin-bottom: 24px; display: flex; align-items:center; gap: 10px;">
        <a href="{{ route('studio.servicios.index') }}" style="color:#fff; text-decoration:none;">←</a>
        {{ isset($servicio) ? 'Editar Servicio Ofertado' : '📝 Crear Nuevo Servicio' }}
    </h1>

    <div class="panel" style="padding: 32px;">
        <form method="POST" action="{{ isset($servicio) ? route('studio.servicios.update') : route('studio.servicios.save') }}">
            @csrf
            @if(isset($servicio))
                <input type="hidden" name="id" value="{{ $servicio->id }}">
            @endif

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Título del Servicio (Packing) <span style="color:#ff5252">*</span></label>
                <input type="text" name="titulo_servicio" value="{{ old('titulo_servicio', $servicio->titulo_servicio ?? '') }}" required class="input" style="width: 100%;" placeholder="Ej. Masterización Premium [Stem Mastering]">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Categoría Técnica</label>
                <select name="tipo_servicio" style="width:100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;">
                    <option value="">Selecciona un tipo...</option>
                    @php
                        $tiposEnum = [
                            'mezcla' => 'Mezcla (Mixing)',
                            'master' => 'Masterización (Mastering)',
                            'otro' => 'Producción / Otros'
                        ];
                    @endphp
                    @foreach($tiposEnum as $valorBD => $etiquetaVisual)
                        <option value="{{ $valorBD }}" {{ (old('tipo_servicio', $servicio->tipo_servicio ?? '') === $valorBD) ? 'selected' : '' }}>
                            {{ $etiquetaVisual }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-3" style="margin-bottom: 24px;">
                <div style="flex:1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Plazo máximo (Días) <span style="color:#ff5252">*</span></label>
                    <input type="number" name="plazo_entrega_dias" value="{{ old('plazo_entrega_dias', $servicio->plazo_entrega_dias ?? '5') }}" required class="input" style="width: 100%;" placeholder="Ej. 7">
                </div>
                <div style="flex:1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nº de Revisiones</label>
                    <input type="number" name="numero_revisiones" value="{{ old('numero_revisiones', $servicio->numero_revisiones ?? '3') }}" class="input" style="width: 100%;" placeholder="Ej. 3">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #00e676;">Precio del Pack (€) <span style="color:#ff5252">*</span></label>
                <input type="number" step="0.01" name="precio_servicio" value="{{ old('precio_servicio', $servicio->precio_servicio ?? '90.00') }}" required class="input" style="width: 100%; border-color: #00e676;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Enlace Portafolio / Ejemplos de Audio (URL)</label>
                <input type="url" name="url_portafolio" value="{{ old('url_portafolio', $servicio->url_portafolio ?? '') }}" class="input" style="width: 100%;" placeholder="https://soundcloud.com/...">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Desglose del contrato/trabajo</label>
                <textarea name="descripcion_servicio" rows="4" class="input" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;" placeholder="Detalla qué incluye el servicio (número máximo de stems, etc)...">{{ old('descripcion_servicio', $servicio->descripcion_servicio ?? '') }}</textarea>
            </div>

            <div style="margin-bottom: 32px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 4px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin:0;">
                    <input type="checkbox" name="servicio_activo" {{ old('servicio_activo', $servicio->servicio_activo ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">Servicio ACTIVO (Aceptar contrataciones)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('studio.servicios.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary" style="padding: 12px 32px;">{{ isset($servicio) ? 'Guardar Oferta' : 'Publicar Servicio' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
