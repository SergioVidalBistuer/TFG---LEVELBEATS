@extends('layouts.master')
@section('title', 'Studio | Mis Beats')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 28px;">📦 Mi Inventario (Beats)</h1>
        <a href="{{ route('studio.beats.create') }}" class="btn btn--primary" style="padding: 10px 16px;">+ Subir Nuevo Beat</a>
    </div>

    @if(session('status'))
        <div style="background-color: #00e676; color:#000; padding:12px; margin-bottom:20px; font-weight:600; border-radius:4px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel" style="padding: 24px;">
        @if($beats->count() === 0)
            <p style="color: rgba(255,255,255,.6);">No tienes beats subidos en tu catálogo aún.</p>
        @else
            <div style="overflow-x: auto;">
                <table class="table-lb" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding-bottom: 12px; text-align: left;">Título</th>
                            <th style="padding-bottom: 12px; text-align: left;">Género / BPM</th>
                            <th style="padding-bottom: 12px; text-align: right;">Precio Base</th>
                            <th style="padding-bottom: 12px; text-align: center;">Estado</th>
                            <th style="padding-bottom: 12px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beats as $beat)
                        <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 14px 0; font-weight: 600;">{{ $beat->titulo_beat }}</td>
                            <td style="padding: 14px 0; color:rgba(255,255,255,.6);">{{ $beat->genero_musical }} ({{ $beat->tempo_bpm }} BPM)</td>
                            <td style="padding: 14px 0; text-align: right; font-weight: 700; color: #00e676;">{{ $beat->precio_base_licencia }} €</td>
                            <td style="padding: 14px 0; text-align: center;">
                                @if($beat->activo_publicado)
                                    <span style="background: rgba(0,230,118,0.1); color: #00e676; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Público</span>
                                @else
                                    <span style="background: rgba(255,255,255,0.1); color: #ccc; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Oculto</span>
                                @endif
                            </td>
                            <td style="padding: 14px 0; text-align: right;">
                                <a href="{{ route('studio.beats.edit', $beat->id) }}" style="color: #fff; margin-right: 12px; text-decoration: underline;">Editar</a>
                                <a href="{{ route('studio.beats.delete', $beat->id) }}" style="color: #ff5252; text-decoration: underline;" onclick="return confirm('¿Seguro que deseas eliminar el beat del inventario?')">Eliminar</a>
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
