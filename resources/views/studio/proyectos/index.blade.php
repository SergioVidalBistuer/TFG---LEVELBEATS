@extends('layouts.master')
@section('title', 'Studio | Mis Proyectos en Curso')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 28px;">📂 Gestor de Proyectos (Ingeniero)</h1>
        <p style="color: rgba(255,255,255,0.6); margin-top: 5px;">Trabajos que han sido comprados por tus clientes y demandan entrega.</p>
    </div>

    @if(session('status'))
        <div style="background-color: #00e676; color:#000; padding:12px; margin-bottom:20px; font-weight:600; border-radius:4px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel" style="padding: 24px;">
        @if($proyectos->count() === 0)
            <p style="color: rgba(255,255,255,.6);">Todavía no tienes proyectos de clientes asignados a tus servicios.</p>
        @else
            <div style="overflow-x: auto;">
                <table class="table-lb" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding-bottom: 12px; text-align: left;">ID / Proyecto</th>
                            <th style="padding-bottom: 12px; text-align: left;">Cliente</th>
                            <th style="padding-bottom: 12px; text-align: center;">Servicio Vinculado</th>
                            <th style="padding-bottom: 12px; text-align: center;">Fecha Creación</th>
                            <th style="padding-bottom: 12px; text-align: center;">Estado</th>
                            <th style="padding-bottom: 12px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $proyecto)
                        <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 14px 0; font-weight: 600;">
                                #{{ $proyecto->id }} - {{ $proyecto->titulo_proyecto }}
                            </td>
                            <td style="padding: 14px 0;">
                                {{ $proyecto->cliente->nombre_usuario ?? 'Desconocido' }}
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
                                <a href="{{ route('studio.proyectos.edit', $proyecto->id) }}" class="btn btn--primary" style="font-size: 12px; padding: 6px 12px;">Gestionar Tarea</a>
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
