@extends('layouts.master')

@section('title', 'Confirmar plan')

@php
    $rolNombre = ucfirst($planRol->rol->nombre_rol);
    $precio = (float) ($planRol->plan->precio_mensual ?? 0);
    $esGratis = $precio === 0.0;
@endphp

@section('content')
<div class="plan-page">
    <header class="plan-hero">
        <p class="studio-eyebrow">Cambiar plan</p>
        <h1>Confirma tu plan profesional</h1>
        <p>Revisa el resumen antes de activar o cambiar tu suscripción de LevelBeats.</p>
    </header>

    @if(session('status'))
        <div class="account-feedback">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="account-feedback account-feedback--error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="account-feedback account-feedback--error">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="plan-checkout">
        <article class="plan-checkout__summary">
            <span class="plan-role-badge">{{ $rolNombre }}</span>
            <h2>{{ $planRol->plan->nombre_plan }}</h2>
            <strong>{{ $esGratis ? 'Gratis' : number_format($precio, 2, ',', '.') . ' €/mes' }}</strong>
            <p>{{ $planRol->plan->beneficios_generales ?? 'Plan profesional de LevelBeats.' }}</p>

            <div class="plan-checkout__current">
                <span>Estado actual</span>
                @if($suscripcionActual)
                    <strong>{{ $suscripcionActual->planPorRol?->plan?->nombre_plan ?? 'Plan activo' }}</strong>
                    <small>Se cancelará al confirmar el nuevo plan.</small>
                @else
                    <strong>Sin plan activo para {{ strtolower($rolNombre) }}</strong>
                    <small>El rol se activará al confirmar este alta.</small>
                @endif
            </div>

            <ul class="plan-checkout__features">
                @if($planRol->rol->nombre_rol === 'productor')
                    <li>{{ $planRol->beats_publicables_mes ?: 0 }} beats publicables al mes</li>
                    <li>{{ $planRol->colecciones_max_productor ?: 0 }} colecciones máximas</li>
                @else
                    <li>{{ $planRol->encargos_max_ingeniero ?: 0 }} encargos simultáneos</li>
                    <li>{{ $planRol->revisiones_incluidas ?: 0 }} revisiones incluidas</li>
                @endif
                <li>{{ $planRol->almacenamiento_gigabytes ?: 0 }} GB de almacenamiento</li>
                <li>Soporte {{ ucfirst($planRol->prioridad_soporte ?? 'basica') }}</li>
            </ul>
        </article>

        <article class="plan-checkout__payment">
            <h2>{{ $esGratis ? 'Confirmación' : 'Continuar al pago' }}</h2>
            <p>
                @if($esGratis)
                    Este plan no requiere pago. Al confirmar se activará la suscripción gratuita.
                @else
                    El plan se tramitará desde el checkout estándar de LevelBeats antes de activar la suscripción.
                @endif
            </p>

            <form method="POST" action="{{ route('usuario.plan.confirmarPago', $planRol) }}">
                @csrf

                <div class="plan-checkout__total">
                    <span>Total mensual</span>
                    <strong>{{ $esGratis ? '0,00 €' : number_format($precio, 2, ',', '.') . ' €' }}</strong>
                </div>

                <div class="plan-checkout__actions">
                    <a href="{{ route('usuario.plan.index') }}" class="btn btn--ghost">Cancelar</a>
                    <button type="submit" class="btn btn--primary">
                        {{ $esGratis ? 'Confirmar plan gratuito' : 'Continuar al pago' }}
                    </button>
                </div>
            </form>
        </article>
    </section>
</div>
@endsection
