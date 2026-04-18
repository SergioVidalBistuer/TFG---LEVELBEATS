@extends('layouts.master')

@section('title', 'Usuarios')

@section('content')

    <div class="section__head">
        <h2>Listado de Usuarios</h2>

        @if(session('rol') === 'admin')
            <a class="btn btn--primary" href="{{ route('usuario.create') }}">Crear Usuario</a>
        @endif
    </div>

    <table class="table-lb">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                @if(session('rol') === 'admin')
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre_usuario }}</td>
                    <td>{{ $usuario->direccion_correo }}</td>
                    <td>
                        <span class="badge">{{ ucfirst($usuario->rol) }}</span>
                    </td>

                    @if(session('rol') === 'admin')
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a class="btn btn--ghost" href="{{ route('usuario.edit', $usuario->id) }}">Editar</a>
                                <a class="btn btn--ghost" href="{{ route('usuario.delete', $usuario->id) }}"
                                   onclick="return confirm('¿Seguro que quieres eliminar a {{ $usuario->nombre_usuario }}?')"
                                   style="border-color: rgba(255,77,77,.3); color: #ff6b6b;">
                                    Eliminar
                                </a>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Volver al listado</a>
    </div>

@endsection
