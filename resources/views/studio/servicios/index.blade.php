@extends('layouts.master')
@section('title', 'Studio | Mis Servicios')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 28px;">🛠️ Mis Servicios (B2B)</h1>
        <a href="{{ route('studio.servicios.create') }}" class="btn btn--primary" style="padding: 10px 16px;">+ Publicar Nuevo Servicio</a>
    </div>

    @if(session('status'))
        <div style="background-color: #00e676; color:#000; padding:12px; margin-bottom:20px; font-weight:600; border-radius:4px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel" style="padding: 24px;">
        @if($servicios->count() === 0)
            <p style="color: rgba(255,255,255,.6);">No tienes servicios publicados. ¡Empieza a ofertar tus habilidades como Ingeniero!</p>
        @else
            <div style="overflow-x: auto;">
                <table class="table-lb" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding-bottom: 12px; text-align: left;">Título / Tipo</th>
                            <th style="padding-bottom: 12px; text-align: center;">Plazo (Días)</th>
                            <th style="padding-bottom: 12px; text-align: center;">Revisiones</th>
                            <th style="padding-bottom: 12px; text-align: right;">Precio</th>
                            <th style="padding-bottom: 12px; text-align: center;">Estado</th>
                            <th style="padding-bottom: 12px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicios as $servicio)
                        <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 14px 0; font-weight: 600;">
                                {{ $servicio->titulo_servicio }}
                                <div style="font-weight: 400; font-size: 13px; color:rgba(255,255,255,.5);">{{ $servicio->tipo_servicio }}</div>
                            </td>
                            <td style="padding: 14px 0; text-align: center; color:rgba(255,255,255,.8);">{{ $servicio->plazo_entrega_dias ?? '-' }}</td>
                            <td style="padding: 14px 0; text-align: center; color:rgba(255,255,255,.8);">{{ $servicio->numero_revisiones ?? '-' }}</td>
                            <td style="padding: 14px 0; text-align: right; font-weight: 700; color: #00e676;">{{ $servicio->precio_servicio }} €</td>
                            <td style="padding: 14px 0; text-align: center;">
                                @if($servicio->servicio_activo)
                                    <span style="background: rgba(0,230,118,0.1); color: #00e676; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Activo</span>
                                @else
                                    <span style="background: rgba(255,255,255,0.1); color: #ccc; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Pausado</span>
                                @endif
                            </td>
                            <td style="padding: 14px 0; text-align: right;">
                                <a href="{{ route('studio.servicios.edit', $servicio->id) }}" style="color: #fff; margin-right: 12px; text-decoration: underline;">Editar</a>
                                <a href="{{ route('studio.servicios.delete', $servicio->id) }}" style="color: #ff5252; text-decoration: underline;" onclick="return confirm('¿Seguro que deseas eliminar el servicio?')">Eliminar</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
