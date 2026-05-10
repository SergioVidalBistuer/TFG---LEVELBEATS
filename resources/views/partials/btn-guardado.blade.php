{{--
    Botón de Guardar / Quitar Guardado
    -----------------------------------------------------------------------
    Parámetros:
    - $tipo    : 'beat' | 'coleccion' | 'servicio'
    - $id      : id del producto
    - $guardado: bool — si el usuario ya lo tiene guardado
    - $compact : bool (opcional) — modo icono pequeño para cards
    -----------------------------------------------------------------------
--}}
@php
    $compact ??= false;
    $estaGuardado = $guardado ?? false;
@endphp

@auth
    <form method="POST" action="{{ route('guardados.toggle') }}"
          class="guardado-form {{ $compact ? 'guardado-form--compact' : '' }}">
        @csrf
        <input type="hidden" name="tipo" value="{{ $tipo }}">
        <input type="hidden" name="id"   value="{{ $id }}">
        <button
            type="submit"
            class="btn-guardado {{ $estaGuardado ? 'btn-guardado--activo' : '' }} {{ $compact ? 'btn-guardado--icon' : '' }}"
            title="{{ $estaGuardado ? 'Quitar de guardados' : 'Guardar' }}"
            aria-label="{{ $estaGuardado ? 'Quitar de guardados' : 'Guardar' }}"
            aria-pressed="{{ $estaGuardado ? 'true' : 'false' }}"
        >
            {{-- Icono bookmark --}}
            <svg width="{{ $compact ? 16 : 18 }}" height="{{ $compact ? 16 : 18 }}"
                 viewBox="0 0 24 24" fill="{{ $estaGuardado ? 'currentColor' : 'none' }}"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 aria-hidden="true">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            @if(!$compact)
                <span>{{ $estaGuardado ? 'Guardado' : 'Guardar' }}</span>
            @endif
        </button>
    </form>
@else
    @if(!$compact)
        <a href="{{ route('login') }}" class="btn-guardado" title="Inicia sesión para guardar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            <span>Guardar</span>
        </a>
    @endif
@endauth
