@extends('layouts.master')

@section('title', 'Admin - Usuarios')

@section('content')
<div class="admin-page">
    <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

    <header class="admin-page__head">
        <div>
            <span class="admin-kicker">Admin</span>
            <h1>Usuarios</h1>
            <p>Gestiona cuentas, roles y datos principales de la plataforma.</p>
        </div>
        <a href="{{ route('usuario.create') }}" class="btn btn--primary">Nuevo usuario</a>
    </header>

    @if(session('status'))
        <div class="admin-feedback">{{ session('status') }}</div>
    @endif

    <section class="admin-table-card">
        <div class="table-responsive">
            <table class="table table-borderless align-middle admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Roles</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                        <tr>
                            <td><span class="admin-id">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                            <td><strong>{{ $user->nombre_usuario }}</strong></td>
                            <td class="admin-muted">{{ $user->direccion_correo }}</td>
                            <td>
                                <div class="admin-badge-row">
                                    @forelse($user->roles as $rol)
                                        @php $rolNombre = strtolower($rol->nombre_rol); @endphp
                                        <span class="admin-badge {{ $rolNombre === 'admin' ? 'admin-badge--accent' : '' }}">
                                            {{ $rol->nombre_rol }}
                                        </span>
                                    @empty
                                        <span class="admin-badge">Cliente</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="admin-actions">
                                    <a href="{{ route('usuario.edit', $user->id) }}">Editar</a>
                                    @if(auth()->id() !== $user->id)
                                        <a class="admin-actions__danger" href="{{ route('usuario.delete', $user->id) }}" onclick="return confirm('¿Confirma que desea eliminar este perfil de forma permanente?')">Borrar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
