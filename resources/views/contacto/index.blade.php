@extends('layouts.master')

@section('title', 'Contacto | LevelBeats')

@section('content')
<div class="contact-page">
    <header class="contact-hero">
        <span class="contact-kicker">Contacto</span>
        <h1>Hablemos</h1>
        <p>Cuéntanos qué necesitas y te responderemos lo antes posible.</p>
    </header>

    @if(session('status'))
        <div class="contact-feedback contact-feedback--success">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="contact-feedback contact-feedback--error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="contact-feedback contact-feedback--error">{{ $errors->first() }}</div>
    @endif

    <div class="contact-grid">
        <section class="contact-card">
            <form class="contact-form" method="POST" action="{{ route('contacto.send') }}" novalidate>
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="contact-honeypot" aria-hidden="true">

                <div class="contact-field">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" class="form-control form-lb__input" type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="120">
                </div>

                <div class="contact-field">
                    <label for="email">Email</label>
                    <input id="email" class="form-control form-lb__input" type="email" name="email" value="{{ old('email') }}" required maxlength="160">
                </div>

                <div class="contact-field">
                    <label for="asunto">Asunto</label>
                    <input id="asunto" class="form-control form-lb__input" type="text" name="asunto" value="{{ old('asunto') }}" maxlength="160">
                </div>

                <div class="contact-field">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" class="form-control form-lb__textarea" name="mensaje" rows="6" required minlength="10" maxlength="3000">{{ old('mensaje') }}</textarea>
                </div>

                <label class="contact-privacy">
                    <input type="checkbox" name="privacidad" value="1" {{ old('privacidad') ? 'checked' : '' }} required>
                    <span>He leído y acepto la <a href="{{ route('legal.privacidad') }}">política de privacidad</a>.</span>
                </label>

                <div class="contact-actions">
                    <button class="btn btn--primary" type="submit">Enviar mensaje</button>
                </div>
            </form>
        </section>

        <aside class="contact-info-card">
            <h2>Información de contacto</h2>
            <div class="contact-info-list">
                <div>
                    <span>Email</span>
                    <strong>contacto@level-beats.com</strong>
                </div>
                <div>
                    <span>Tiempo de respuesta</span>
                    <strong>24-48 horas laborables</strong>
                </div>
                <div>
                    <span>Motivos habituales</span>
                    <p>Soporte, colaboraciones, incidencias, consultas generales y ayuda con productos o servicios.</p>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
