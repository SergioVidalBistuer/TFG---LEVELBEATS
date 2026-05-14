@php
    $variant = $variant ?? 'card';
    $role = $role ?? 'Productor';
    $usuario = $usuario ?? null;
    $isDetail = $variant === 'detail';
    $perfilPublico = $usuario && ($usuario->perfil_publico ?? false) && Route::has('perfiles.show');
    $nombre = $usuario?->nombre_usuario ?? 'Usuario no disponible';
@endphp

@if($perfilPublico)
<a href="{{ route('perfiles.show', $usuario) }}" class="product-owner product-owner--{{ $variant }} is-link">
@else
<div class="product-owner product-owner--{{ $variant }} is-muted">
@endif
    <span class="product-owner__avatar">
        @if($usuario?->url_foto_perfil)
            <img src="{{ asset($usuario->url_foto_perfil) }}" alt="{{ $nombre }}">
        @else
            {{ strtoupper(substr($nombre, 0, 1)) }}
        @endif
    </span>

    <span class="product-owner__body">
        @if($isDetail)
            <span class="product-owner__eyebrow">{{ $role }}</span>
        @endif
        <strong>{{ $nombre }}</strong>
        @if($isDetail)
            <small>
                {{ $usuario?->descripcion_perfil ?: 'Perfil profesional de LevelBeats.' }}
            </small>
        @endif
    </span>

    @if($isDetail)
        <span class="product-owner__action">
            {{ $perfilPublico ? 'Ver perfil' : 'Perfil no público' }}
        </span>
    @endif
@if($perfilPublico)
</a>
@else
</div>
@endif
