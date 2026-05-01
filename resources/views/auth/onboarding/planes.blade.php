@extends('layouts.master')

@section('title', 'Selecciona tu Plan')

@section('content')
<div style="max-width: 1000px; margin: 40px auto; text-align: center;">

    <h1 style="font-size: 32px; margin-bottom: 8px;">Planes para {{ ucfirst($rolName) }}</h1>
    <p style="color: rgba(255,255,255,0.6); margin-bottom: 40px; font-size: 16px;">Elige tu licencia de trabajo. Facturación mensual (Alta Simulada MVP).</p>

    @if($planesPorRol->isEmpty())
        <div style="padding: 40px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: rgba(255,255,255,0.5);">
            No hay planes configurados para este rol aún en la Base de Datos. Contacta con soporte.
            <br>
            <a href="{{ route('beat.index') }}" class="btn btn--primary mt-4">Continuar al Inicio</a>
        </div>
    @else
        <div class="row justify-content-center g-4" style="text-align: left;">
            @foreach($planesPorRol as $ppr)
                <div class="col-md-4">
                    @php
                        $isPro = stripos($ppr->plan->nombre_plan, 'pro') !== false || stripos($ppr->plan->nombre_plan, 'premium') !== false;
                        $borderColor = $isPro ? '#00d4ff' : 'rgba(255,255,255,0.1)';
                        $boxShadow = $isPro ? '0 8px 24px rgba(0, 212, 255, 0.15)' : '0 4px 12px rgba(0,0,0,0.1)';
                        $transform = $isPro ? 'transform: translateY(-8px); z-index: 2;' : 'transition: transform 0.2s;';
                        $btnColor = $isPro ? '#00d4ff' : '#00e676';
                    @endphp
                    
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid {{ $borderColor }}; border-radius: 8px; padding: 24px; display: flex; flex-direction: column; height: 100%; box-shadow: {{ $boxShadow }}; position: relative; {{ $transform }}">
                        
                        @if($isPro)
                            <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #00d4ff; color: #000; font-size: 11px; font-weight: bold; text-transform: uppercase; padding: 4px 12px; border-radius: 12px;">
                                Recomendado
                            </div>
                        @endif

                        <h3 style="margin-top: 0; color: #fff;">{{ $ppr->plan->nombre_plan }}</h3>
                        
                        <p style="font-size: 28px; font-weight: 700; color: {{ $btnColor }}; margin: 12px 0;">
                            @if($ppr->plan->precio_mensual == 0)
                                Gratis
                            @else
                                €{{ number_format($ppr->plan->precio_mensual, 2) }} <span style="font-size: 14px; font-weight: 400; color: rgba(255,255,255,0.5);">/ mes</span>
                            @endif
                        </p>
                        
                        <div style="flex-grow: 1; font-size: 14px; color: rgba(255,255,255,0.8); margin-bottom: 24px;">
                            @if($ppr->plan->beneficios_generales)
                                <p style="font-style: italic; font-size: 13px; color: rgba(255,255,255,0.5); margin-bottom: 20px;">
                                    "{{ $ppr->plan->beneficios_generales }}"
                                </p>
                            @endif
                            <ul style="list-style: none; padding-left: 0; margin: 0; line-height: 1.8;">
                                @if($ppr->beats_publicables_mes > 0)
                                    <li><span style="color: {{ $btnColor }}; font-weight: bold; margin-right: 6px;">✓</span> <strong>{{ $ppr->beats_publicables_mes }}</strong> Beats publicables/mes</li>
                                @endif
                                @if($ppr->almacenamiento_gigabytes > 0)
                                    <li><span style="color: {{ $btnColor }}; font-weight: bold; margin-right: 6px;">✓</span> <strong>{{ $ppr->almacenamiento_gigabytes }}GB</strong> cloud storage</li>
                                @endif
                                @if($ppr->encargos_max_ingeniero > 0)
                                    <li><span style="color: {{ $btnColor }}; font-weight: bold; margin-right: 6px;">✓</span> <strong>{{ $ppr->encargos_max_ingeniero }}</strong> Encargos simultáneos</li>
                                @endif
                                @if($ppr->prioridad_soporte != 'basica')
                                    <li><span style="color: {{ $btnColor }}; font-weight: bold; margin-right: 6px;">✓</span> Soporte {{ ucfirst($ppr->prioridad_soporte) }}</li>
                                @else
                                    <li><span style="color: rgba(255,255,255,0.3); font-weight: bold; margin-right: 6px;">✓</span> Soporte estándar</li>
                                @endif
                            </ul>
                        </div>

                        <form action="{{ route('onboarding.subscribe') }}" method="POST" style="margin-top: auto;">
                            @csrf
                            <input type="hidden" name="id_plan_rol" value="{{ $ppr->id }}">
                            <input type="hidden" name="id_rol" value="{{ $rol->id }}">
                            <button type="submit" class="btn btn--primary" style="width: 100%; {{ $isPro ? 'background: #00d4ff; color: #000; border-color:#00d4ff;' : 'background: transparent; color: #fff; border-color: rgba(255,255,255,0.3);' }}" onclick="return confirm('¿Avanzar con la suscripción a {{ $ppr->plan->nombre_plan }}?');">
                                Activar Plan
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
