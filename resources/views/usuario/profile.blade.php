@extends('layouts.master')

@section('title', 'Mi Perfil')

@section('content')

    {{-- CABECERA DEL PERFIL --}}
    <div class="profile-header">
        <div class="profile-avatar">
            @if($usuario->url_foto_perfil)
                <img src="{{ asset($usuario->url_foto_perfil) }}" alt="{{ $usuario->nombre_usuario }}">
            @else
                {{ strtoupper(substr($usuario->nombre_usuario, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <h1>{{ $usuario->nombre_usuario }}</h1>
            <p>{{ $usuario->direccion_correo }}</p>
            @php
                $badges = [];
                
                if ($usuario->esAdmin()) {
                    $badges[] = ['nombre' => 'Admin System', 'color' => '#ff5252', 'bg' => 'rgba(255,82,82,0.1)'];
                }
                if ($usuario->tieneSuscripcionActiva('productor')) {
                    $badges[] = ['nombre' => 'Productor', 'color' => '#00e676', 'bg' => 'rgba(0,230,118,0.1)'];
                }
                if ($usuario->tieneSuscripcionActiva('ingeniero')) {
                    $badges[] = ['nombre' => 'Ingeniero', 'color' => '#00d4ff', 'bg' => 'rgba(0,212,255,0.1)'];
                }
                
                // Si carece de suscripciones vigentes
                if (empty($badges)) {
                    $badges[] = ['nombre' => 'Cliente Base', 'color' => 'rgba(255,255,255,0.7)', 'bg' => 'rgba(255,255,255,0.05)'];
                }
            @endphp

            <p style="margin-top:8px; display: flex; align-items: center; gap: 8px;">
                @foreach($badges as $b)
                    <span style="font-size: 11px; padding: 4px 10px; border-radius: 4px; border: 1px solid {{ str_replace('0.1', '0.2', $b['bg']) }}; background: {{ $b['bg'] }}; color: {{ $b['color'] }}; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        {{ $b['nombre'] }}
                    </span>
                @endforeach
                @if($usuario->verificacion_completada)
                    <span style="color: #00e676; font-size: 12px; margin-left: 8px;">✓ Verificado</span>
                @endif
            </p>
            
            @if(auth()->check() && auth()->id() === $usuario->id)
                <div style="margin-top: 16px;">
                    <a href="{{ route('onboarding.roles') }}" class="btn btn--ghost" style="border-color: #00e676; color: #00e676; padding: 6px 16px; font-size: 13px;">
                        ⚡ Cambiar plan / Hazte creador
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- SUSCRIPCIONES B2B ACTIVAS --}}
    @php
        // Lectura pura (La DB ya ha sido saneada por el UsuarioController antes de renderizar)
        $suscripcionesActivas = $usuario->suscripciones()
            ->with(['planPorRol.plan', 'planPorRol.rol'])
            ->where('estado_suscripcion', 'activa')
            ->orderByDesc('fecha_inicio')
            ->get();
    @endphp

    @foreach($suscripcionesActivas as $suscripcionActiva)
        <div style="background: rgba(0, 212, 255, 0.05); border: 1px solid rgba(0,212,255,0.2); padding: 20px 24px; margin-bottom: 24px; border-radius: 8px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
            <div>
                <h3 style="margin: 0; color: #00d4ff; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    Suscripción Pro: {{ $suscripcionActiva->planPorRol->plan->nombre_plan }}
                </h3>
                <p style="margin: 6px 0 0 0; color: rgba(255,255,255,0.8); font-size: 14px;">
                    Licencia oficial para el ecosistema de <strong>{{ ucfirst($suscripcionActiva->planPorRol->rol->nombre_rol) }}</strong>.
                </p>
                <div style="margin-top: 8px; font-size: 12px; color: rgba(255,255,255,0.5); display: flex; gap: 12px;">
                    <span>Estado: <strong style="color:#00e676;">{{ ucfirst($suscripcionActiva->estado_suscripcion) }}</strong></span>
                    <span>Facturación: {{ ucfirst($suscripcionActiva->tipo_pago) }}</span>
                    <span>Desde: {{ \Carbon\Carbon::parse($suscripcionActiva->fecha_inicio)->format('d/m/Y') }}</span>
                </div>

                @php
                    $rolTag = $suscripcionActiva->planPorRol->rol->nombre_rol;
                    $limite = 0;
                    $uso = 0;
                    $textoUso = "";
                    
                    if ($rolTag === 'productor') {
                        $limite = $suscripcionActiva->planPorRol->beats_publicables_mes;
                        $uso = $usuario->beats->count();
                        $textoUso = "Consumo de Beats";
                    } elseif ($rolTag === 'ingeniero') {
                        $limite = $suscripcionActiva->planPorRol->encargos_max_ingeniero;
                        $uso = $usuario->servicios->count();
                        $textoUso = "Servicios Ofertados";
                    }
                    
                    $porcentaje = $limite > 0 ? min(100, round(($uso / $limite) * 100)) : 0;
                    $isIlimitado = $limite >= 90;
                @endphp

                @if($limite > 0)
                <div style="margin-top: 16px; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); width: 100%; max-width: 280px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                        <span style="color: rgba(255,255,255,0.7);">{{ $textoUso }}</span>
                        <strong style="color: #fff;">{{ $uso }} / {{ $isIlimitado ? '∞' : $limite }}</strong>
                    </div>
                    @if(!$isIlimitado)
                    <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $porcentaje }}%; background: {{ $porcentaje >= 90 ? '#ff5252' : '#00d4ff' }}; transition: width 0.3s ease;"></div>
                    </div>
                    @else
                    <div style="font-size: 11px; color: #00e676; font-style: italic;">Sin restricciones de tope (Plan Premium)</div>
                    @endif
                </div>
                @endif
            </div>
            <div>
                @if(auth()->check() && auth()->id() === $usuario->id)
                    <a href="{{ route('onboarding.roles') }}" class="btn btn--primary" style="background: transparent; color: #00d4ff; border-color: #00d4ff; white-space: nowrap;">
                        Mejorar Plan
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    {{-- ESTADÍSTICAS --}}
    <div class="profile-stats">
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->beats->count() }}</span>
            <span class="profile-stat__label">Beats</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->colecciones->count() }}</span>
            <span class="profile-stat__label">Colecciones</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->comprasComoComprador->count() }}</span>
            <span class="profile-stat__label">Compras</span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat__number">{{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') : '-' }}</span>
            <span class="profile-stat__label">Miembro desde</span>
        </div>
    </div>

    {{-- INFO PERSONAL --}}
    @if($usuario->descripcion_perfil || $usuario->localidad || $usuario->pais)
        <div class="panel panel--dark" style="padding: 24px; margin-bottom: 32px;">
            <h3 style="margin-top: 0;">Sobre mí</h3>
            @if($usuario->descripcion_perfil)
                <p>{{ $usuario->descripcion_perfil }}</p>
            @endif
            @if($usuario->localidad || $usuario->provincia || $usuario->pais)
                <p style="color: rgba(255,255,255,.5); font-size: 14px;">
                    📍 {{ collect([$usuario->localidad, $usuario->provincia, $usuario->pais])->filter()->implode(', ') }}
                </p>
            @endif
        </div>
    @endif

    {{-- MIS BEATS --}}
    @if($usuario->beats->count())
        <h2>Mis Beats</h2>
        <div class="grid grid--4">
            @foreach($usuario->beats as $beat)
                <article class="card">
                    <div class="card__media">
                        <img src="{{ asset($beat->url_portada_beat) }}"
                             alt="Portada {{ $beat->titulo_beat }}"
                             style="width:100%;height:128px;object-fit:cover;">
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $beat->titulo_beat }}</h3>
                        <p class="card__meta">Género: {{ $beat->genero_musical ?? '-' }}</p>
                        <div class="card__foot">
                            <span class="price">{{ $beat->precio_base_licencia }} €</span>
                            <div class="card__actions">
                                <a class="btn btn--ghost" href="{{ route('beat.detail', $beat->id) }}">Ver</a>
                                <a class="btn btn--ghost" href="{{ route('beat.edit', $beat->id) }}">Editar</a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- MIS COLECCIONES --}}
    @if($usuario->colecciones->count())
        <h2 style="margin-top: 32px;">Mis Colecciones</h2>
        <div class="grid grid--4">
            @foreach($usuario->colecciones as $coleccion)
                <article class="card">
                    <div class="card__media">
                        @if($coleccion->beats->first() && $coleccion->beats->first()->url_portada_beat)
                            <img src="{{ asset($coleccion->beats->first()->url_portada_beat) }}"
                                 alt="Portada {{ $coleccion->titulo_coleccion }}"
                                 style="width:100%;height:128px;object-fit:cover;">
                        @else
                            <div style="width:100%;height:128px;background:linear-gradient(135deg, var(--primary), #1a1a2e);display:flex;align-items:center;justify-content:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="card__body">
                        <h3 class="card__title">{{ $coleccion->titulo_coleccion }}</h3>
                        <p class="card__meta">{{ $coleccion->beats->count() }} beats</p>
                        <div class="card__foot">
                            <span class="price">{{ $coleccion->precio ?? '—' }} €</span>
                            <div class="card__actions">
                                <a class="btn btn--ghost" href="{{ route('coleccion.detail', $coleccion->id) }}">Ver</a>
                                @if(auth()->check() && auth()->user()->esAdmin())
                                    <a class="btn btn--ghost" href="{{ route('coleccion.edit', $coleccion->id) }}">Editar</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

@endsection
