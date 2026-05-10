@php
    $listasArchivos = [
        'Archivos del cliente' => [
            'items' => $archivosCliente ?? collect(),
            'empty' => 'Todavía no hay archivos del cliente.',
        ],
        'Archivos del ingeniero' => [
            'items' => $archivosIngeniero ?? collect(),
            'empty' => 'Todavía no hay archivos del ingeniero.',
        ],
    ];
@endphp

<div class="shared-files-box">
    <div class="shared-files-box__head">
        <div>
            <h4>Archivos compartidos del encargo</h4>
            <p>Aquí se comparten audios, referencias, stems y entregas entre cliente e ingeniero.</p>
        </div>
        <span>{{ count($archivos) }} archivos</span>
    </div>

    <div class="shared-files-groups">
        @foreach($listasArchivos as $tituloLista => $configLista)
            <section class="shared-files-group">
                <h5>{{ $tituloLista }}</h5>

                @if($configLista['items']->isEmpty())
                    <p class="shared-files-group__empty">{{ $configLista['empty'] }}</p>
                @else
                    <ul class="shared-files-list">
                        @foreach($configLista['items'] as $archivo)
                            @php
                                $nombreArchivo = preg_replace('/^[0-9a-f-]{36}_/', '', basename($archivo->archivo));
                                $fechaArchivo = $archivo->fecha_subida
                                    ? $archivo->fecha_subida->format('d/m/Y H:i')
                                    : 'Fecha no registrada';
                            @endphp
                            <li>
                                <span>
                                    <strong>{{ $nombreArchivo }}</strong>
                                    <small>{{ $archivo->usuario->nombre_usuario ?? 'Usuario no registrado' }} · {{ $fechaArchivo }}</small>
                                </span>
                                <a href="{{ route('proyectos.archivos.download', ['id' => $proyecto->id, 'file' => $archivo->id]) }}" class="btn btn--ghost btn-sm">Descargar</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach

        @if(($archivosSinAutor ?? collect())->isNotEmpty())
            <section class="shared-files-group">
                <h5>Archivos sin autor registrado</h5>
                <ul class="shared-files-list">
                    @foreach($archivosSinAutor as $archivo)
                        @php
                            $nombreArchivo = preg_replace('/^[0-9a-f-]{36}_/', '', basename($archivo->archivo));
                            $fechaArchivo = $archivo->fecha_subida
                                ? $archivo->fecha_subida->format('d/m/Y H:i')
                                : 'Fecha no registrada';
                        @endphp
                        <li>
                            <span>
                                <strong>{{ $nombreArchivo }}</strong>
                                <small>Autor no registrado · {{ $fechaArchivo }}</small>
                            </span>
                            <a href="{{ route('proyectos.archivos.download', ['id' => $proyecto->id, 'file' => $archivo->id]) }}" class="btn btn--ghost btn-sm">Descargar</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    @if($cancelado)
        <div class="shared-files-disabled">El servicio está cancelado. La subida de archivos queda bloqueada.</div>
    @else
        <form class="project-inline-form project-inline-form--file" action="{{ route('proyectos.archivos.upload', $proyecto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo" required accept="audio/*,.zip,.rar,.7z,.wav,.mp3,.aiff,.aif,.flac,.m4a" class="form-control form-lb__input project-file-input">
            <button type="submit" class="btn btn--primary btn-sm">Subir archivo</button>
        </form>
    @endif
    @error('archivo')
        <p class="text-danger small mt-2">{{ $message }}</p>
    @enderror
</div>
