@extends('layouts.master')

@section('title', 'Admin - Listado de Usuarios')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.dashboard.index') }}" style="color: #ff5252; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px;">
            &larr; Volver al Dashboard Root
        </a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="margin-bottom: 4px; color: #fff;">Gestión de Usuarios</h1>
                <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 15px;">Panel para auditar, editar y dar de baja cuentas de la plataforma.</p>
            </div>
            <div>
                <a href="{{ route('usuario.create') }}" class="btn btn--primary" style="background: transparent; color: #00e676; border-color: rgba(0,230,118,0.5);">+ Nuevo Usuario</a>
            </div>
        </div>
    </div>

    <div style="overflow-x: auto; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; padding: 12px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.2);">
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">ID</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Nombre de Usuario</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Correo Electrónico</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase;">Roles del Sistema</th>
                    <th style="padding: 14px 16px; color: rgba(255,255,255,0.5); font-weight: 600; font-size: 12px; text-transform: uppercase; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $user)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px; color: rgba(255,255,255,0.4); font-family: monospace;">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="padding: 16px; font-weight: 500; color: #fff;">
                            {{ $user->nombre_usuario }}
                        </td>
                        <td style="padding: 16px; color: rgba(255,255,255,0.7);">
                            {{ $user->direccion_correo }}
                        </td>
                        <td style="padding: 16px;">
                            @if($user->roles->isEmpty())
                                <span style="color: rgba(255,255,255,0.3); font-style: italic; font-size: 13px;">Cliente Base</span>
                            @else
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    @foreach($user->roles as $rol)
                                        @php
                                            $rName = strtolower($rol->nombre_rol);
                                            $color = 'rgba(255,255,255,0.7)';
                                            $bg = 'rgba(255,255,255,0.05)';
                                            $border = 'rgba(255,255,255,0.1)';
                                            
                                            if ($rName === 'admin') {
                                                $color = '#ff5252'; $bg = 'rgba(255,82,82,0.1)'; $border = 'rgba(255,82,82,0.2)';
                                            } elseif ($rName === 'productor') {
                                                $color = '#00e676'; $bg = 'rgba(0,230,118,0.1)'; $border = 'rgba(0,230,118,0.2)';
                                            } elseif ($rName === 'ingeniero') {
                                                $color = '#00d4ff'; $bg = 'rgba(0,212,255,0.1)'; $border = 'rgba(0,212,255,0.2)';
                                            }
                                        @endphp
                                        <span style="background: {{ $bg }}; color: {{ $color }}; border: 1px solid {{ $border }}; border-radius: 4px; padding: 3px 8px; font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">
                                            {{ $rol->nombre_rol }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('usuario.edit', $user->id) }}" style="color: #00d4ff; font-size: 13px; text-decoration: none; font-weight: 500; padding: 4px 10px; border: 1px solid rgba(0,212,255,0.3); border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,212,255,0.1)'" onmouseout="this.style.background='transparent'">Editar</a>
                                @if(auth()->id() !== $user->id)
                                    <a href="{{ route('usuario.delete', $user->id) }}" onclick="return confirm('¿Confirma que desea banear / eliminar este perfil de forma permanente?')" style="color: #ff5252; font-size: 13px; text-decoration: none; font-weight: 500; padding: 4px 10px; border: 1px solid rgba(255,82,82,0.3); border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,82,82,0.1)'" onmouseout="this.style.background='transparent'">Borrar</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
