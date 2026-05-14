@extends('layouts.master')
@section('title', isset($servicio) ? 'Editar Servicio' : 'Nuevo Servicio')

@section('content')
<div class="studio-page studio-page--form">
    <div class="studio-form-head">
        <a class="btn btn--ghost" href="{{ route('studio.servicios.index') }}">Volver</a>
        <div>
            <p class="studio-eyebrow">Studio · Servicios</p>
            <h1>{{ isset($servicio) ? 'Editar servicio' : 'Crear servicio' }}</h1>
            <p class="muted">Define una oferta técnica clara para clientes de mezcla, mastering o producción.</p>
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
        <form class="studio-form" method="POST" action="{{ isset($servicio) ? route('studio.servicios.update') : route('studio.servicios.save') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($servicio))
                <input type="hidden" name="id" value="{{ $servicio->id }}">
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <div class="studio-field">
                        <label for="titulo_servicio">Título del servicio <span>*</span></label>
                        <input id="titulo_servicio" type="text" name="titulo_servicio" value="{{ old('titulo_servicio', $servicio->titulo_servicio ?? '') }}" required class="form-control form-lb__input" placeholder="Ej. Masterización premium">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="tipo_servicio">Categoría técnica <span>*</span></label>
                        <select id="tipo_servicio" name="tipo_servicio" class="form-select form-lb__select" required>
                            <option value="">Selecciona un tipo</option>
                            @foreach(['mezcla' => 'Mezcla', 'master' => 'Masterización', 'otro' => 'Producción / Otros'] as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ old('tipo_servicio', $servicio->tipo_servicio ?? '') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="studio-field">
                        <label for="plazo_entrega_dias">Plazo <span>*</span></label>
                        <input id="plazo_entrega_dias" type="number" name="plazo_entrega_dias" value="{{ old('plazo_entrega_dias', $servicio->plazo_entrega_dias ?? '5') }}" required class="form-control form-lb__input" placeholder="5">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="studio-field">
                        <label for="numero_revisiones">Revisiones</label>
                        <input id="numero_revisiones" type="number" name="numero_revisiones" value="{{ old('numero_revisiones', $servicio->numero_revisiones ?? '3') }}" class="form-control form-lb__input" placeholder="3">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="precio_servicio">Precio del pack (€) <span>*</span></label>
                        <input id="precio_servicio" type="number" step="0.01" name="precio_servicio" value="{{ old('precio_servicio', $servicio->precio_servicio ?? '90.00') }}" required class="form-control form-lb__input" placeholder="90.00">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="studio-field">
                        <label for="url_portafolio">Portafolio / ejemplos</label>
                        <input id="url_portafolio" type="url" name="url_portafolio" value="{{ old('url_portafolio', $servicio->url_portafolio ?? '') }}" class="form-control form-lb__input" placeholder="https://soundcloud.com/...">
                    </div>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label for="descripcion_servicio">Descripción del trabajo</label>
                        <textarea id="descripcion_servicio" name="descripcion_servicio" class="form-control form-lb__textarea" placeholder="Detalla qué incluye el servicio, entregables y condiciones...">{{ old('descripcion_servicio', $servicio->descripcion_servicio ?? '') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <div class="studio-field">
                        <label for="portada_servicio">Portada del servicio</label>
                        <input
                            id="portada_servicio"
                            type="file"
                            name="portada_servicio"
                            class="form-control form-lb__input project-file-input studio-cover-input"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >
                        <small>
                            @if(isset($servicio) && $servicio->portada_url)
                                Portada actual: {{ basename($servicio->portada_url) }}. Sube otra imagen solo si quieres reemplazarla.
                            @else
                                Formatos admitidos: JPG, PNG o WEBP. Máximo 5 MB.
                            @endif
                        </small>
                    </div>
                </div>

                <div class="col-12">
                    <label class="studio-switch studio-switch--compact">
                        <input type="checkbox" name="servicio_activo" {{ old('servicio_activo', $servicio->servicio_activo ?? true) ? 'checked' : '' }}>
                        <span>
                            <strong>Servicio activo</strong>
                            <small>Si está pausado, no aparecerá en el catálogo público de servicios.</small>
                        </span>
                    </label>
                </div>
            </div>

            <div class="studio-form-actions">
                <a href="{{ route('studio.servicios.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary">{{ isset($servicio) ? 'Guardar cambios' : 'Publicar servicio' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
