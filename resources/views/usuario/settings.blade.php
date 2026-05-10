@extends('layouts.master')

@section('title', 'Ajustes de la cuenta | LevelBeats')

@section('content')
    <div class="section__head account-settings__head">
        <div>
            <h2>Ajustes de la cuenta</h2>
            <p class="muted" style="margin:6px 0 0;">Gestiona tus datos, foto de perfil y seguridad.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('usuario.profile') }}">Ver perfil</a>
    </div>

    @if($errors->any())
        <div class="account-feedback account-feedback--error">{{ $errors->first() }}</div>
    @endif

    <div class="account-settings">
        <section class="panel account-settings__panel">
            <h3>Información personal</h3>
            <form method="POST" action="{{ route('usuario.settings.profile') }}">
                @csrf

                <div class="form-lb__row">
                    <div class="form-lb__group">
                        <label for="nombre_usuario">Nombre de usuario</label>
                        <input id="nombre_usuario" class="form-control form-lb__input" type="text" name="nombre_usuario" value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" required>
                    </div>
                    <div class="form-lb__group">
                        <label for="direccion_correo">Correo electrónico</label>
                        <input id="direccion_correo" class="form-control form-lb__input" type="email" name="direccion_correo" value="{{ old('direccion_correo', $usuario->direccion_correo) }}" required>
                    </div>
                </div>

                <div class="form-lb__group">
                    <label for="descripcion_perfil">Descripción de perfil</label>
                    <textarea id="descripcion_perfil" class="form-control form-lb__textarea" name="descripcion_perfil" placeholder="Cuéntanos sobre tu perfil musical...">{{ old('descripcion_perfil', $usuario->descripcion_perfil) }}</textarea>
                </div>

                <div class="form-lb__row">
                    <div class="form-lb__group">
                        <label for="calle">Calle</label>
                        <input id="calle" class="form-control form-lb__input" type="text" name="calle" value="{{ old('calle', $usuario->calle) }}">
                    </div>
                    <div class="form-lb__group">
                        <label for="localidad">Localidad</label>
                        <input id="localidad" class="form-control form-lb__input" type="text" name="localidad" value="{{ old('localidad', $usuario->localidad) }}">
                    </div>
                </div>

                <div class="form-lb__row">
                    <div class="form-lb__group">
                        <label for="provincia">Provincia</label>
                        <input id="provincia" class="form-control form-lb__input" type="text" name="provincia" value="{{ old('provincia', $usuario->provincia) }}">
                    </div>
                    <div class="form-lb__group">
                        <label for="pais">País</label>
                        <input id="pais" class="form-control form-lb__input" type="text" name="pais" value="{{ old('pais', $usuario->pais) }}">
                    </div>
                </div>

                <div class="form-lb__group">
                    <label for="codigo_postal">Código postal</label>
                    <input id="codigo_postal" class="form-control form-lb__input" type="text" name="codigo_postal" value="{{ old('codigo_postal', $usuario->codigo_postal) }}">
                </div>

                <div class="form-lb__actions">
                    <button class="btn btn--primary" type="submit">Guardar cambios</button>
                </div>
            </form>
        </section>

        <aside class="account-settings__side">
            <section class="panel account-settings__panel account-settings__panel--compact">
                <h3>Foto de perfil</h3>
                <div class="account-avatar">
                    @if($usuario->url_foto_perfil)
                        <img src="{{ asset($usuario->url_foto_perfil) }}" alt="{{ $usuario->nombre_usuario }}">
                    @else
                        <span>{{ strtoupper(substr($usuario->nombre_usuario, 0, 1)) }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('usuario.settings.photo') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-lb__group">
                        <label for="foto_perfil">Nueva imagen</label>
                        <div class="account-file-picker">
                            <input id="foto_perfil" class="account-file-picker__input" type="file" name="foto_perfil" accept="image/jpeg,image/png,image/webp" required>
                            <label class="account-file-picker__control" for="foto_perfil">
                                <span>Seleccionar archivo</span>
                                <strong data-file-name>Ningún archivo seleccionado</strong>
                            </label>
                        </div>
                    </div>
                    <button class="btn btn--primary w-100" type="submit">Actualizar foto</button>
                </form>
            </section>

            <section class="panel account-settings__panel account-settings__panel--compact">
                <h3>Seguridad</h3>
                <form method="POST" action="{{ route('usuario.settings.password') }}">
                    @csrf
                    <div class="form-lb__group">
                        <label for="contrasena_actual">Contraseña actual</label>
                        <input id="contrasena_actual" class="form-control form-lb__input" type="password" name="contrasena_actual" required>
                    </div>
                    <div class="form-lb__group">
                        <label for="nueva_contrasena">Nueva contraseña</label>
                        <input id="nueva_contrasena" class="form-control form-lb__input" type="password" name="nueva_contrasena" required>
                    </div>
                    <div class="form-lb__group">
                        <label for="nueva_contrasena_confirmation">Confirmar nueva contraseña</label>
                        <input id="nueva_contrasena_confirmation" class="form-control form-lb__input" type="password" name="nueva_contrasena_confirmation" required>
                    </div>
                    <button class="btn btn--primary w-100" type="submit">Cambiar contraseña</button>
                </form>
            </section>
        </aside>
    </div>

    <script>
        document.addEventListener('change', function(event) {
            const input = event.target.closest('.account-file-picker__input');
            if (!input) return;

            const picker = input.closest('.account-file-picker');
            const fileName = picker ? picker.querySelector('[data-file-name]') : null;
            if (!fileName) return;

            fileName.textContent = input.files && input.files.length
                ? input.files[0].name
                : 'Ningún archivo seleccionado';
        });
    </script>
@endsection
