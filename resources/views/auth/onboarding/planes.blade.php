@extends('layouts.master')

@section('title', 'Selecciona tu Plan')

@section('content')
<div class="onboarding-page onboarding-page--plans">
    <header class="onboarding-hero">
        <span class="studio-eyebrow">Planes profesionales</span>
        <h1>Planes para {{ ucfirst($rolName) }}</h1>
        <p>Elige el plan que encaja con tu actividad. Revisarás el resumen antes de confirmar el alta.</p>
    </header>

    @if($planesPorRol->isEmpty())
        <div class="onboarding-empty">
            No hay planes configurados para este rol aún en la Base de Datos. Contacta con soporte.
            <a href="{{ route('beat.index') }}" class="btn btn--primary">Continuar al inicio</a>
        </div>
    @else
        <div class="onboarding-plan-grid">
            @foreach($planesPorRol as $ppr)
                @php $isRecommended = $loop->iteration === 2; @endphp
                <article class="onboarding-plan-card {{ $isRecommended ? 'is-recommended' : '' }}">
                    @if($isRecommended)
                        <span class="onboarding-plan-card__flag">Recomendado</span>
                    @endif

                    <h2>{{ $ppr->plan->nombre_plan }}</h2>
                    <strong class="onboarding-plan-card__price">
                        @if($ppr->plan->precio_mensual == 0)
                            Gratis
                        @else
                            {{ number_format($ppr->plan->precio_mensual, 2, ',', '.') }} € <span>/ mes</span>
                        @endif
                    </strong>

                    @if($ppr->plan->beneficios_generales)
                        <p>{{ $ppr->plan->beneficios_generales }}</p>
                    @endif

                    <ul>
                        @if($ppr->beats_publicables_mes > 0)
                            <li><strong>{{ $ppr->beats_publicables_mes }}</strong> beats publicables/mes</li>
                        @endif
                        @if($ppr->almacenamiento_gigabytes > 0)
                            <li><strong>{{ $ppr->almacenamiento_gigabytes }} GB</strong> de almacenamiento</li>
                        @endif
                        @if($ppr->encargos_max_ingeniero > 0)
                            <li><strong>{{ $ppr->encargos_max_ingeniero }}</strong> encargos simultáneos</li>
                        @endif
                        <li>Soporte {{ ucfirst($ppr->prioridad_soporte ?? 'basica') }}</li>
                    </ul>

                    <form action="{{ route('onboarding.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_plan_rol" value="{{ $ppr->id }}">
                        <input type="hidden" name="id_rol" value="{{ $rol->id }}">
                        <button type="submit" class="btn {{ $isRecommended ? 'btn--primary' : 'btn--ghost' }}">
                            Revisar y continuar
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
