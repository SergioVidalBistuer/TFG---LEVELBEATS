@extends('layouts.master')

@section('title', 'Cambiar plan')

@php
    $rolesProfesionales = ['productor' => 'Productor', 'ingeniero' => 'Ingeniero'];
    $accent = ['productor' => 'green', 'ingeniero' => 'cyan'];
    $estadoLabel = [
        'activa' => 'Activa',
        'pendiente' => 'Pendiente',
        'cancelada' => 'Cancelada',
        'expirada' => 'Expirada',
    ];
@endphp

@section('content')
<div class="plan-page">
    <header class="plan-hero">
        <p class="studio-eyebrow">Mi Área</p>
        <h1>Gestión de roles y planes</h1>
        <p>Centraliza tus roles profesionales y las suscripciones activas de LevelBeats.</p>
    </header>

    @if(session('status'))
        <div class="account-feedback">{{ session('status') }}</div>
    @endif

    <section class="plan-section">
        <div class="plan-section__head">
            <div>
                <h2>Estado actual</h2>
                <p>Resumen de acceso y suscripciones asociadas a tu cuenta.</p>
            </div>
        </div>

        <div class="plan-status-grid">
            <article class="plan-card">
                <span class="plan-role-badge">Cliente / Usuario básico</span>
                <h3>{{ $usuario->nombre_usuario }}</h3>
                <p>{{ $usuario->direccion_correo }}</p>
                <div class="plan-meta-list">
                    <span>Acceso gratuito</span>
                    <span>Comprar, guardar, contactar y contratar servicios</span>
                </div>
            </article>

            @foreach($rolesProfesionales as $rolKey => $rolNombre)
                @php
                    $suscripciones = $suscripcionesPorRol->get($rolKey, collect());
                    $activa = $suscripciones->firstWhere('estado_suscripcion', 'activa');
                    $rolActivo = $rolesActivos->contains($rolKey);
                    $estado = $activa?->estado_suscripcion ?? ($rolActivo ? 'pendiente' : null);
                @endphp
                <article class="plan-card plan-card--{{ $accent[$rolKey] }}">
                    <span class="plan-role-badge">{{ $rolNombre }}</span>
                    <h3>{{ $activa?->planPorRol?->plan?->nombre_plan ?? ($rolActivo ? 'Rol activo sin plan' : 'No activo') }}</h3>
                    @if($estado)
                        <span class="plan-state plan-state--{{ $estado }}">{{ $estadoLabel[$estado] ?? ucfirst($estado) }}</span>
                    @else
                        <span class="plan-state">Disponible</span>
                    @endif
                    <div class="plan-meta-list">
                        @if($activa)
                            <span>Inicio: {{ $activa->fecha_inicio ? \Carbon\Carbon::parse($activa->fecha_inicio)->format('d/m/Y') : '-' }}</span>
                            <span>Fin: {{ $activa->fecha_fin ? \Carbon\Carbon::parse($activa->fecha_fin)->format('d/m/Y') : 'Sin fecha' }}</span>
                            <span>Facturación: {{ ucfirst($activa->tipo_pago ?? 'mensual') }}</span>
                        @else
                            <span>{{ $rolActivo ? 'Elige un plan para completar el rol.' : 'Puedes activarlo cuando lo necesites.' }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="plan-section">
        <div class="plan-section__head">
            <div>
                <h2>Gestión de roles</h2>
                <p>El rol base siempre permanece activo. Los roles profesionales se activan con un plan.</p>
            </div>
        </div>

        <div class="plan-role-grid">
            @foreach($rolesProfesionales as $rolKey => $rolNombre)
                @php
                    $rolActivo = $rolesActivos->contains($rolKey);
                    $activa = $suscripcionesPorRol->get($rolKey, collect())->firstWhere('estado_suscripcion', 'activa');
                @endphp
                <article class="plan-role-card plan-role-card--{{ $accent[$rolKey] }}">
                    <div>
                        <span class="plan-role-badge">{{ $rolNombre }}</span>
                        <h3>{{ $rolActivo ? 'Rol profesional activo' : 'Activar rol ' . $rolNombre }}</h3>
                        <p>
                            @if($rolKey === 'productor')
                                Publica beats, crea colecciones y gestiona tu catálogo musical desde Studio.
                            @else
                                Publica servicios técnicos, recibe encargos y gestiona proyectos desde Studio.
                            @endif
                        </p>
                    </div>

                    @if($rolActivo)
                        <form method="POST" action="{{ route('usuario.plan.cancelarRol') }}" onsubmit="return confirm('¿Seguro que quieres desactivar el rol {{ $rolNombre }}? No se borrará tu histórico.');">
                            @csrf
                            <input type="hidden" name="rol" value="{{ $rolKey }}">
                            <button class="btn btn--ghost btn--danger" type="submit">Cancelar rol</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('usuario.plan.activarRol') }}">
                            @csrf
                            <input type="hidden" name="rol" value="{{ $rolKey }}">
                            <button class="btn btn--primary" type="submit">Activar rol {{ $rolNombre }}</button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="plan-section">
        <div class="plan-section__head">
            <div>
                <h2>Gestión de planes</h2>
                <p>Los planes de pago se tramitan desde el checkout estándar de LevelBeats antes de activar la suscripción.</p>
            </div>
        </div>

        @foreach($rolesProfesionales as $rolKey => $rolNombre)
            @php
                $rol = $roles->get($rolKey);
                $rolActivo = $rolesActivos->contains($rolKey);
                $suscripcionActiva = $suscripcionesPorRol->get($rolKey, collect())->firstWhere('estado_suscripcion', 'activa');
                $planActivoId = $suscripcionActiva?->id_plan_rol;
                $planes = $planesPorRol->get($rolKey, collect());
            @endphp

            <div class="plan-block">
                <div class="plan-block__head">
                    <h3>{{ $rolNombre }}</h3>
                    @if($suscripcionActiva)
                        <span>Plan actual: {{ $suscripcionActiva->planPorRol->plan->nombre_plan ?? 'No registrado' }}</span>
                    @else
                        <span>{{ $rolActivo ? 'Rol activo pendiente de plan' : 'Rol no activo' }}</span>
                    @endif
                </div>

                @if(!$rolActivo)
                    <div class="analytics-empty">
                        Activa el rol {{ strtolower($rolNombre) }} para consultar sus planes.
                    </div>
                @elseif($planes->isEmpty())
                    <div class="analytics-empty">
                        No hay planes configurados para este rol.
                    </div>
                @else
                    <div class="plan-options-grid">
                        @foreach($planes as $planRol)
                            @php $esActual = (int) $planActivoId === (int) $planRol->id; @endphp
                            <article class="plan-option {{ $esActual ? 'is-current' : '' }}">
                                <div>
                                    <span class="plan-role-badge">{{ $esActual ? 'Plan actual' : 'Plan disponible' }}</span>
                                    <h4>{{ $planRol->plan->nombre_plan }}</h4>
                                    <strong>{{ (float) $planRol->plan->precio_mensual === 0.0 ? 'Gratis' : number_format($planRol->plan->precio_mensual, 2, ',', '.') . ' €/mes' }}</strong>
                                    <p>{{ $planRol->plan->beneficios_generales ?? 'Plan profesional de LevelBeats.' }}</p>
                                    <ul>
                                        @if($rolKey === 'productor')
                                            <li>{{ $planRol->beats_publicables_mes ?: 0 }} beats publicables/mes</li>
                                            <li>{{ $planRol->colecciones_max_productor ?: 0 }} colecciones máximas</li>
                                        @else
                                            <li>{{ $planRol->encargos_max_ingeniero ?: 0 }} encargos simultáneos</li>
                                            <li>{{ $planRol->revisiones_incluidas ?: 0 }} revisiones incluidas</li>
                                        @endif
                                        <li>{{ $planRol->almacenamiento_gigabytes ?: 0 }} GB de almacenamiento</li>
                                        <li>Soporte {{ ucfirst($planRol->prioridad_soporte ?? 'basica') }}</li>
                                    </ul>
                                </div>

                                @if($esActual)
                                    <button class="btn btn--ghost" type="button" disabled>Mantener plan actual</button>
                                @else
                                    <a class="btn btn--primary" href="{{ route('usuario.plan.checkout', $planRol) }}">
                                        {{ $suscripcionActiva ? 'Cambiar plan' : 'Activar plan' }}
                                    </a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </section>
</div>
@endsection
