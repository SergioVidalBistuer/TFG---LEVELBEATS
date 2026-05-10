@extends('layouts.master')

@section('title', $servicio->titulo_servicio . ' – Servicios LevelBeats')

@section('content')

    {{-- HEADER DEL SERVICIO --}}
    <div style="margin-bottom:10px;">
        <a href="{{ route('servicio.index') }}" class="btn btn--ghost"
           style="font-size:12px;padding:5px 14px;margin-bottom:20px;display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Volver a Servicios
        </a>
    </div>

    @php
        $gradients = [
            'mezcla' => 'linear-gradient(135deg,#00c6ff,#0072ff)',
            'master' => 'linear-gradient(135deg,#a900ef,#6200ea)',
            'otro'   => 'linear-gradient(135deg,#00e676,#007a3d)',
        ];
        $grad = $gradients[$servicio->tipo_servicio] ?? 'linear-gradient(135deg,#444,#222)';

        $tipoLabel = [
            'mezcla' => 'Mezcla',
            'master' => 'Mastering',
            'otro'   => 'Otro',
        ][$servicio->tipo_servicio] ?? ucfirst($servicio->tipo_servicio);
    @endphp

    <div class="collection" style="gap:40px;">

        {{-- COLUMNA IZQUIERDA: detalles --}}
        <div style="flex:1;min-width:0;">

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
                <span class="badge" style="background:{{ $grad }};color:#fff;">
                    {{ $tipoLabel }}
                </span>
                @if($servicio->plazo_entrega_dias)
                    <span class="badge" style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.7);">
                        🕐 {{ $servicio->plazo_entrega_dias }} días de entrega
                    </span>
                @endif
                @if($servicio->numero_revisiones !== null)
                    <span class="badge" style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.7);">
                        🔄 {{ $servicio->numero_revisiones }} revisiones
                    </span>
                @endif
            </div>

            <h1 style="margin-bottom:8px;font-size:clamp(22px,4vw,36px);line-height:1.2;">
                {{ $servicio->titulo_servicio }}
            </h1>

            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
                <div class="price" style="font-size:28px;margin:0;">
                    {{ number_format($servicio->precio_servicio, 2) }} €
                </div>
                {{-- Botón Guardar en detalle servicio --}}
                @include('partials.btn-guardado', [
                    'tipo'    => 'servicio',
                    'id'      => $servicio->id,
                    'guardado'=> $estaGuardado,
                    'compact' => false,
                ])
            </div>

            @if($servicio->descripcion_servicio)
                <div class="panel panel--dark" style="margin-bottom:24px;">
                    <h3 style="margin-top:0;font-size:14px;text-transform:uppercase;
                               letter-spacing:.6px;color:rgba(255,255,255,.45);margin-bottom:10px;">
                        Descripción del servicio
                    </h3>
                    <p style="color:rgba(255,255,255,.8);line-height:1.7;margin:0;">
                        {{ $servicio->descripcion_servicio }}
                    </p>
                </div>
            @endif

            {{-- INFO INGENIERO --}}
            @if($servicio->usuario)
                <a class="panel panel--dark" id="seccion-ingeniero"
                     href="{{ route('usuario.profile.public', $servicio->usuario->id) }}"
                     style="display:flex;align-items:center;gap:16px;margin-bottom:24px;flex-wrap:wrap;text-decoration:none;color:inherit;border-color:rgba(169,0,239,0.22);transition:border-color .15s, background .15s;"
                     onmouseover="this.style.borderColor='rgba(210,107,255,0.55)';this.style.background='rgba(169,0,239,0.08)'"
                     onmouseout="this.style.borderColor='rgba(169,0,239,0.22)';this.style.background=''">
                    <div style="width:56px;height:56px;border-radius:50%;flex-shrink:0;
                                background:linear-gradient(135deg,#A900EF,#D26BFF);
                                display:flex;align-items:center;justify-content:center;
                                font-size:22px;font-weight:800;color:#0b0b0f;">
                        {{ strtoupper(substr($servicio->usuario->nombre_usuario, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;
                                    color:rgba(255,255,255,.4);margin-bottom:3px;">Ingeniero</div>
                        <div style="font-size:17px;font-weight:700;color:#fff;">
                            {{ $servicio->usuario->nombre_usuario }}
                        </div>
                        @if($servicio->usuario->descripcion_perfil)
                            <div style="font-size:13px;color:rgba(255,255,255,.55);margin-top:4px;
                                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $servicio->usuario->descripcion_perfil }}
                            </div>
                        @endif
                    </div>
                </a>
            @endif

            {{-- PORTAFOLIO --}}
            @if($servicio->url_portafolio)
                <div style="margin-bottom:24px;">
                    <a href="{{ $servicio->url_portafolio }}" target="_blank" rel="noopener noreferrer"
                       class="btn btn--ghost"
                       style="display:inline-flex;align-items:center;gap:8px;font-size:13px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Ver portafolio del ingeniero
                    </a>
                </div>
            @endif

            {{-- CTA CONTACTO --}}
            <div id="cta-contacto"
                 style="background:linear-gradient(135deg,rgba(0,198,255,.08),rgba(169,0,239,.08));
                        border:1px solid rgba(169,0,239,.25);border-radius:16px;padding:24px;">
                <h3 style="margin:0 0 8px;font-size:18px;">¿Interesado en este servicio?</h3>
                <p style="margin:0 0 18px;color:rgba(255,255,255,.6);font-size:14px;">
                    Contacta directamente con el ingeniero para discutir los detalles de tu proyecto.
                </p>

                @if(auth()->check())
                    @if(!auth()->user()->esAdmin() && auth()->id() !== $servicio->id_usuario)
                        {{-- FORMULARIO DE CONTACTO --}}
                        <form id="form-contacto-servicio" method="POST" action="{{ route('servicio.contacto', $servicio->id) }}"
                              style="display:flex;flex-direction:column;gap:12px;">
                            @csrf
                            <textarea name="mensaje" id="mensaje-contacto" rows="4"
                                      placeholder="Describe brevemente tu proyecto: género, referencias, plazo estimado..."
                                      required
                                      style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
                                             border-radius:10px;color:#fff;padding:12px 14px;font-size:14px;
                                             resize:vertical;font-family:inherit;width:100%;box-sizing:border-box;
                                             min-height:100px;"></textarea>
                            <button type="submit" id="btn-enviar-contacto" class="btn btn--primary"
                                    style="align-self:flex-start;display:inline-flex;align-items:center;gap:8px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                Enviar mensaje al ingeniero
                            </button>
                        </form>
                    @elseif(auth()->id() === $servicio->id_usuario)
                        <p style="color:rgba(255,255,255,.45);font-size:14px;margin:0;">
                            Este es tu propio servicio.
                        </p>
                    @endif
                @else
                    <a href="{{ route('login') }}" id="btn-login-para-contactar" class="btn btn--primary"
                       style="display:inline-flex;align-items:center;gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Inicia sesión para contactar
                    </a>
                @endif
            </div>

            {{-- ADMIN / PROPIETARIO --}}
            @if(auth()->check() && (auth()->user()->esAdmin() || auth()->id() === $servicio->id_usuario))
                <div class="admin-actions d-flex gap-2" style="margin-top:20px;">
                    <a class="btn btn--ghost"
                       href="{{ route('studio.servicios.edit', $servicio->id) }}">
                        Editar
                    </a>
                    <a class="btn btn--ghost"
                       style="color:#ff4d4d;border-color:rgba(255,77,77,.3);"
                       href="{{ route('studio.servicios.delete', $servicio->id) }}"
                       onclick="return confirm('¿Seguro que quieres eliminar este servicio?')">
                        Eliminar
                    </a>
                </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: visual --}}
        <div class="collection__right"
             style="overflow:hidden;min-height:320px;border-radius:var(--radius-sm);
                    background:{{ $grad }};
                    display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;">

            @if($servicio->tipo_servicio === 'mezcla')
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.2">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                    <circle cx="7" cy="6" r="2" fill="rgba(255,255,255,0.7)"/>
                    <circle cx="17" cy="12" r="2" fill="rgba(255,255,255,0.7)"/>
                    <circle cx="10" cy="18" r="2" fill="rgba(255,255,255,0.7)"/>
                </svg>
            @elseif($servicio->tipo_servicio === 'master')
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            @else
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.2">
                    <path d="M9 19V6l12-3v13"/><circle cx="6" cy="19" r="3"/><circle cx="18" cy="16" r="3"/>
                </svg>
            @endif

            <div style="text-align:center;padding:0 20px;">
                <div style="font-size:28px;font-weight:800;color:#fff;">
                    {{ number_format($servicio->precio_servicio, 0) }} €
                </div>
                <div style="font-size:13px;color:rgba(255,255,255,.65);margin-top:4px;">
                    {{ $tipoLabel }}
                    @if($servicio->plazo_entrega_dias)
                        · {{ $servicio->plazo_entrega_dias }} días
                    @endif
                </div>
            </div>

            {{-- SPECS --}}
            <div style="background:rgba(0,0,0,.25);border-radius:10px;padding:16px 20px;
                        width:calc(100% - 40px);margin:0 20px;">
                <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px;">
                    <li style="display:flex;justify-content:space-between;font-size:13px;color:rgba(255,255,255,.8);">
                        <span>Tipo</span>
                        <strong>{{ $tipoLabel }}</strong>
                    </li>
                    @if($servicio->plazo_entrega_dias)
                        <li style="display:flex;justify-content:space-between;font-size:13px;color:rgba(255,255,255,.8);">
                            <span>Entrega</span>
                            <strong>{{ $servicio->plazo_entrega_dias }} días</strong>
                        </li>
                    @endif
                    @if($servicio->numero_revisiones !== null)
                        <li style="display:flex;justify-content:space-between;font-size:13px;color:rgba(255,255,255,.8);">
                            <span>Revisiones</span>
                            <strong>{{ $servicio->numero_revisiones }}</strong>
                        </li>
                    @endif
                    @if($servicio->usuario)
                        <li style="display:flex;justify-content:space-between;font-size:13px;color:rgba(255,255,255,.8);">
                            <span>Ingeniero</span>
                            <strong>{{ $servicio->usuario->nombre_usuario }}</strong>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

@endsection
