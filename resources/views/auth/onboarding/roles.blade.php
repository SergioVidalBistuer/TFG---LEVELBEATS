@extends('layouts.master')

@section('title', 'Bienvenido - Elige tu camino')

@section('content')
<div class="onboarding-page">
    <header class="onboarding-hero">
        <span class="studio-eyebrow">Onboarding</span>
        <h1>¿Cómo vas a usar LevelBeats?</h1>
        <p>Selecciona tu vía principal. Podrás modificarla o ampliarla desde tu cuenta cuando lo necesites.</p>
    </header>

    <div class="onboarding-role-grid">
        @foreach([
            ['key' => 'cliente', 'title' => 'Cliente / Usuario básico', 'copy' => 'Compra instrumentales, guarda productos, contacta con perfiles y encarga servicios profesionales.', 'button' => 'Continuar como cliente', 'tone' => 'neutral'],
            ['key' => 'productor', 'title' => 'Productor musical', 'copy' => 'Publica beats, crea colecciones y gestiona licencias para vender tu catálogo musical.', 'button' => 'Activar Productor', 'tone' => 'green'],
            ['key' => 'ingeniero', 'title' => 'Ingeniero de sonido', 'copy' => 'Ofrece mezcla, mastering y servicios técnicos para recibir encargos desde Studio.', 'button' => 'Activar Ingeniero', 'tone' => 'cyan'],
        ] as $role)
            <article class="onboarding-role-card onboarding-role-card--{{ $role['tone'] }}">
                <span class="onboarding-role-card__kicker">{{ $role['key'] === 'cliente' ? 'Acceso base' : 'Rol profesional' }}</span>
                <h2>{{ $role['title'] }}</h2>
                <p>{{ $role['copy'] }}</p>

                <form action="{{ route('onboarding.setRole') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role['key'] }}">
                    <button type="submit" class="btn {{ $role['key'] === 'cliente' ? 'btn--ghost' : 'btn--primary' }}">
                        {{ $role['button'] }}
                    </button>
                </form>
            </article>
        @endforeach
    </div>
</div>
@endsection
