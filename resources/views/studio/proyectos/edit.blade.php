@extends('layouts.master')
@section('title', 'Studio | Gestionar Proyecto #' . $proyecto->id)

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <h1 style="font-size: 24px; margin-bottom: 24px; display: flex; align-items:center; gap: 10px;">
        <a href="{{ route('studio.proyectos.index') }}" style="color:#fff; text-decoration:none;">←</a>
        Gestionar Tarea: {{ $proyecto->titulo_proyecto }}
    </h1>

    <div class="panel" style="padding: 32px;">
        <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h3 style="margin-top: 0;">Detalles de la Contratación</h3>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 4px;"><strong>Cliente:</strong> {{ $proyecto->cliente->nombre_usuario ?? 'Desconocido' }} ({{ $proyecto->cliente->direccion_correo ?? '' }})</p>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 4px;"><strong>Servicio Pagado:</strong> {{ $proyecto->servicio->titulo_servicio ?? '-' }}</p>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 0;"><strong>Fecha Activación:</strong> {{ $proyecto->fecha_creacion ?? '-' }}</p>
        </div>

        <form method="POST" action="{{ route('studio.proyectos.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $proyecto->id }}">

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Estado del Proyecto</label>
                <select name="estado_proyecto" style="width:100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;">
                    <option value="Pendiente de Archivos" {{ $proyecto->estado_proyecto == 'Pendiente de Archivos' ? 'selected' : '' }}>Pendiente de Archivos del Cliente</option>
                    <option value="En Proceso/Mezclando" {{ $proyecto->estado_proyecto == 'En Proceso/Mezclando' ? 'selected' : '' }}>En Proceso (Trabajando en Estudio)</option>
                    <option value="En Revisión" {{ $proyecto->estado_proyecto == 'En Revisión' ? 'selected' : '' }}>En Fase de Revisión (Feedback)</option>
                    <option value="Entregado" {{ $proyecto->estado_proyecto == 'Entregado' ? 'selected' : '' }}>Finalizado y Entregado</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Cuaderno de Notas Privadas (Solo lo ves tú)</label>
                <textarea name="notas_proyecto" rows="5" class="input" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;" placeholder="Ej. El cliente quiere el bajo más subido que en su maqueta...">{{ old('notas_proyecto', $proyecto->notas_proyecto ?? '') }}</textarea>
            </div>

            <!-- LISTADO DE ARCHIVOS -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; margin-bottom: 24px;">
                <h4 style="margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">Stems y Archivos del Trabajo</h4>
                
                @if(count($archivos) === 0)
                    <p style="color: rgba(255,255,255,0.5); font-size: 13px; margin: 0;">No hay archivos cargados en el contenedor de este proyecto.</p>
                @else
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($archivos as $archivo)
                            <li style="padding: 10px 0; border-bottom: 1px dashed rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 14px;">📄 {{ basename($archivo) }}</span>
                                <a href="{{ route('proyectos.archivos.download', ['id' => $proyecto->id, 'file' => $archivo]) }}" class="btn btn--ghost" style="font-size: 12px; padding: 4px 10px; color: #00e676; border-color: rgba(0,230,118,0.3);">⬇ Descargar</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form action="{{ route('proyectos.archivos.upload', $proyecto->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 20px; display: flex; gap: 8px; align-items: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
                    @csrf
                    <input type="file" name="archivo" required class="input" style="flex: 1; padding: 6px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;">
                    <button type="submit" class="btn btn--primary">Anexar Fichero</button>
                </form>
            </div>

            <!-- CHAT ASÍNCRONO DEL PROYECTO -->
            <div style="background: rgba(255,255,255,0.02); padding: 24px; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; margin-bottom: 24px;">
                <h4 style="margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">Mensajes con el Cliente</h4>
                
                <div style="max-height: 400px; overflow-y: auto; margin-bottom: 16px; display: flex; flex-direction: column; gap: 12px; padding-right: 8px;">
                    @forelse($proyecto->mensajes as $msg)
                        @php $esMio = $msg->id_usuario_emisor === auth()->id(); @endphp
                        <div style="display: flex; flex-direction: column; align-items: {{ $esMio ? 'flex-end' : 'flex-start' }};">
                            <span style="font-size: 11px; color:rgba(255,255,255,0.4); margin-bottom: 2px;">{{ $msg->emisor->nombre_usuario ?? '...' }} - {{ \Carbon\Carbon::parse($msg->fecha_envio)->format('d/m H:i') }}</span>
                            <div style="background: {{ $esMio ? 'rgba(0, 230, 118, 0.1)' : 'rgba(255, 255, 255, 0.05)' }}; color: {{ $esMio ? '#00e676' : '#fff' }}; padding: 10px 14px; border-radius: 8px; border: 1px solid {{ $esMio ? 'rgba(0, 230, 118, 0.2)' : 'rgba(255, 255, 255, 0.1)' }}; max-width: 80%;">
                                {{ $msg->contenido_mensaje }}
                            </div>
                        </div>
                    @empty
                        <p style="color: rgba(255,255,255,0.5); font-size: 13px; text-align: center;">No hay mensajes en este panel todavía.</p>
                    @endforelse
                </div>

                <form action="{{ route('mensajes.proyecto.enviar', $proyecto->id) }}" method="POST" style="display: flex; gap: 8px;">
                    @csrf
                    <input type="text" name="contenido_mensaje" class="input" style="flex: 1;" placeholder="Escribe un mensaje al Cliente..." required autocomplete="off">
                    <button type="submit" class="btn btn--primary">Enviar</button>
                </form>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('studio.proyectos.index') }}" class="btn btn--ghost">Cancelar</a>
                <button type="submit" class="btn btn--primary" style="padding: 12px 32px;">Actualizar Progreso</button>
            </div>
        </form>
    </div>
</div>
@endsection
