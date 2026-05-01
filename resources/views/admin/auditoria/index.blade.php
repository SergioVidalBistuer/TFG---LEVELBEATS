@extends('layouts.master')

@section('title', 'Panel de Auditoría Básico')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.dashboard.index') }}" style="color: #ff5252; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px;">
            &larr; Volver al Dashboard Root
        </a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="margin-bottom: 4px; color: #fff;">Logs de Auditoría</h1>
                <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 15px;">Registro inalterable de operaciones críticas de plataforma.</p>
            </div>
        </div>
    </div>

    <div style="overflow-x: auto; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 12px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.2);">
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Acontecimiento</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Usuario Responsable</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Acción Ejecutada</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Módulo Afectado</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Vector (ID)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $log)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px; color: rgba(255,255,255,0.5); font-size: 13px;">
                            {{ \Carbon\Carbon::parse($log->fecha)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($log->fecha)->format('H:i') }}
                        </td>
                        <td style="padding: 16px; font-weight: 500; color: #fff;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 24px; height: 24px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: rgba(255,255,255,0.5);">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </div>
                                {{ $log->actor->nombre_usuario ?? 'Cuenta Eliminada (Target #'.$log->id_usuario_actor.')' }}
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $accion = strtolower($log->tipo_accion);
                                $color = 'rgba(255,255,255,0.7)';
                                $bg = 'rgba(255,255,255,0.05)';
                                $border = 'rgba(255,255,255,0.1)';
                                
                                if ($accion === 'crear' || $accion === 'insertar') {
                                    $color = '#00e676'; $bg = 'rgba(0,230,118,0.1)'; $border = 'rgba(0,230,118,0.2)';
                                } elseif ($accion === 'eliminar' || $accion === 'borrar') {
                                    $color = '#ff5252'; $bg = 'rgba(255,82,82,0.1)'; $border = 'rgba(255,82,82,0.2)';
                                } elseif ($accion === 'actualizar' || $accion === 'editar') {
                                    $color = '#00d4ff'; $bg = 'rgba(0,212,255,0.1)'; $border = 'rgba(0,212,255,0.2)';
                                }
                            @endphp
                            <span style="background: {{ $bg }}; color: {{ $color }}; border: 1px solid {{ $border }}; border-radius: 4px; padding: 3px 8px; font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">
                                {{ $log->tipo_accion }}
                            </span>
                        </td>
                        <td style="padding: 16px; color: rgba(255,255,255,0.8);">
                            <strong>{{ ucfirst($log->entidad) }}</strong>
                        </td>
                        <td style="padding: 16px; font-family: monospace; color: rgba(255,255,255,0.4);">
                            #{{ str_pad($log->id_entidad, 4, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: rgba(255,255,255,0.5);">No hay eventos de auditoría registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $registros->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
