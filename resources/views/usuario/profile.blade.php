@extends('layouts.master')

@section('title', auth()->check() && auth()->id() === $usuario->id ? 'Mi Perfil' : 'Perfil de ' . $usuario->nombre_usuario)

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
                    $badges[] = ['nombre' => 'Admin System'];
                }
                if ($usuario->tieneSuscripcionActiva('productor')) {
                    $badges[] = ['nombre' => 'Productor'];
                }
                if ($usuario->tieneSuscripcionActiva('ingeniero')) {
                    $badges[] = ['nombre' => 'Ingeniero'];
                }
                
                // Si carece de suscripciones vigentes
                if (empty($badges)) {
                    $badges[] = ['nombre' => 'Cliente Base'];
                }
            @endphp

            <p class="profile-badge-row">
                @foreach($badges as $b)
                    <span class="profile-role">
                        {{ $b['nombre'] }}
                    </span>
                @endforeach
                @if($usuario->verificacion_completada)
                    <span class="profile-role profile-role--verified">Verificado</span>
                @endif
            </p>
            
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
        <div class="profile-subscription-card">
            <div>
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    Suscripción Pro: {{ $suscripcionActiva->planPorRol->plan->nombre_plan }}
                </h3>
                <p>
                    Licencia oficial para el ecosistema de <strong>{{ ucfirst($suscripcionActiva->planPorRol->rol->nombre_rol) }}</strong>.
                </p>
                <div class="profile-subscription-card__meta">
                    <span>Estado: <strong>{{ ucfirst($suscripcionActiva->estado_suscripcion) }}</strong></span>
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
                <div class="profile-usage">
                    <div class="profile-usage__head">
                        <span>{{ $textoUso }}</span>
                        <strong>{{ $uso }} / {{ $isIlimitado ? '∞' : $limite }}</strong>
                    </div>
                    @if(!$isIlimitado)
                    <div class="profile-usage__bar">
                        <span style="width: {{ $porcentaje }}%;"></span>
                    </div>
                    @else
                    <div class="profile-usage__unlimited">Sin restricciones de tope</div>
                    @endif
                </div>
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
        <div class="panel panel--dark profile-about">
            <h3>Sobre mí</h3>
            @if($usuario->descripcion_perfil)
                <p>{{ $usuario->descripcion_perfil }}</p>
            @endif
            @if($usuario->localidad || $usuario->provincia || $usuario->pais)
                <p class="profile-location">
                    {{ collect([$usuario->localidad, $usuario->provincia, $usuario->pais])->filter()->implode(', ') }}
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
                        <img src="{{ \App\Support\Imagenes::portada($beat->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                             alt="Portada {{ $beat->titulo_beat }}"
                             loading="lazy"
                             decoding="async"
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
                        <img src="{{ \App\Support\Imagenes::portada($coleccion->portada_url ?? $coleccion->beats->first()?->url_portada_beat ?? 'media/img/nocheDeAmor.jpg') }}"
                             alt="Portada {{ $coleccion->titulo_coleccion }}"
                             loading="lazy"
                             decoding="async"
                             style="width:100%;height:128px;object-fit:cover;">
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
