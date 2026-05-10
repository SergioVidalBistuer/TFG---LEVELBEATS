@extends('layouts.master')
@section('title', 'Studio | Mis Colecciones')

@section('content')
<div class="studio-page">
    <div class="studio-page__head">
        <div>
            <p class="studio-eyebrow">Studio</p>
            <h1>Mis Colecciones</h1>
            <p class="muted">Agrupa tus beats, define precios y gestiona packs desde Studio.</p>
        </div>
        <a href="{{ route('studio.colecciones.create') }}" class="btn btn--primary">Crear colección</a>
    </div>

    <section class="studio-panel">
        @if($colecciones->count() === 0)
            <div class="studio-empty">
                <h2>No tienes colecciones todavía</h2>
                <p class="muted">Crea una colección para vender varios beats como un pack completo.</p>
                <a class="btn btn--primary" href="{{ route('studio.colecciones.create') }}">Crear colección</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo / Género</th>
                            <th class="text-center">Beats</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Destacada</th>
                            <th class="text-center">Visibilidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colecciones as $coleccion)
                        <tr>
                            <td>
                                <strong>{{ $coleccion->titulo_coleccion }}</strong>
                            </td>
                            <td class="studio-table__muted">
                                {{ $coleccion->tipo_coleccion ?: 'Sin tipo' }}
                                @if($coleccion->estilo_genero)
                                    · {{ $coleccion->estilo_genero }}
                                @endif
                            </td>
                            <td class="text-center">{{ $coleccion->beats_count }}</td>
                            <td class="text-end fw-bold">{{ number_format($coleccion->precio, 2, ',', '.') }} €</td>
                            <td class="text-center">
                                @if($coleccion->es_destacada)
                                    <span class="studio-badge studio-badge--public">Destacada</span>
                                @else
                                    <span class="studio-badge">Normal</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($coleccion->activo_publicado)
                                    <span class="studio-badge studio-badge--public">Visible</span>
                                @else
                                    <span class="studio-badge">Oculta</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="studio-actions">
                                    <a href="{{ route('coleccion.detail', $coleccion->id) }}">Ver</a>
                                    <a href="{{ route('studio.colecciones.edit', $coleccion->id) }}">Editar</a>
                                    <a class="studio-actions__danger" href="{{ route('studio.colecciones.delete', $coleccion->id) }}" onclick="return confirm('¿Seguro que deseas eliminar esta colección?')">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
