@extends('layouts.master')
@section('title', 'Mis Encargos')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 28px;">🎧 Mis Encargos (Servicios Contratados)</h1>
        <p style="color: rgba(255,255,255,0.6); margin-top: 5px;">Seguimiento en directo del progreso de los trabajos que el Ingeniero está realizando para ti.</p>
    </div>

    <div class="panel" style="padding: 24px;">
        @if($proyectos->count() === 0)
            <div style="text-align: center; padding: 40px;">
                <p style="font-size: 16px; color: rgba(255,255,255,.6);">Aún no has encargado ningún servicio técnico.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-lb" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding-bottom: 12px; text-align: left;">Trabajo / Título</th>
                            <th style="padding-bottom: 12px; text-align: left;">Ingeniero a cargo</th>
                            <th style="padding-bottom: 12px; text-align: center;">Servicio Adquirido</th>
                            <th style="padding-bottom: 12px; text-align: center;">Fecha Solicitud</th>
                            <th style="padding-bottom: 12px; text-align: center;">Estado Actual</th>
                            <th style="padding-bottom: 12px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $proyecto)
                        <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 14px 0; font-weight: 600;">{{ $proyecto->titulo_proyecto }}</td>
                            <td style="padding: 14px 0;">
                                {{ $proyecto->servicio->usuario->nombre_usuario ?? 'Desconocido' }}
                            </td>
                            <td style="padding: 14px 0; text-align: center; color:rgba(255,255,255,.8);">
                                {{ $proyecto->servicio->titulo_servicio ?? '-' }}
                            </td>
                            <td style="padding: 14px 0; text-align: center; color:rgba(255,255,255,.5);">
                                {{ $proyecto->fecha_creacion ? \Carbon\Carbon::parse($proyecto->fecha_creacion)->format('d/m/Y') : '-' }}
                            </td>
                            <td style="padding: 14px 0; text-align: center;">
                                @if($proyecto->estado_proyecto === 'Completado' || $proyecto->estado_proyecto === 'Entregado')
                                    <span style="background: rgba(0,230,118,0.1); color: #00e676; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $proyecto->estado_proyecto }}</span>
                                @else
                                    <span style="background: rgba(255,193,7,0.1); color: #ffc107; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $proyecto->estado_proyecto ?? 'Pendiente' }}</span>
                                @endif
                            </td>
                            <td style="padding: 14px 0; text-align: right;">
                                <a href="{{ route('usuario.encargos.detail', $proyecto->id) }}" class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;">Consultar Progreso</a>
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
