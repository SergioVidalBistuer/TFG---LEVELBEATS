<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArchivoProyecto;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchivoProyectoController extends Controller
{
    public function upload(Request $request, $id_proyecto)
    {
        $request->validate([
            'archivo' => 'required|file|max:51200',
        ], [
            'archivo.required' => 'Selecciona un archivo para subir.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.max' => 'El archivo no puede superar los 50 MB.',
            'archivo.uploaded' => 'No se pudo subir el archivo. Comprueba que no supere los 50 MB y vuelve a intentarlo.',
        ]);

        $proyecto = Proyecto::with('servicio')->findOrFail($id_proyecto);
        $usuario_actual = auth()->id();

        // Validar participación (Cliente contratante o Ingeniero ejecutor) o Admin
        $esCliente = ($proyecto->id_usuario === $usuario_actual);
        $esIngeniero = ($proyecto->servicio && $proyecto->servicio->id_usuario === $usuario_actual);

        if (!$esCliente && !$esIngeniero && !auth()->user()->esAdmin()) {
            abort(403, 'No estás autorizado para subir archivos a este proyecto.');
        }

        if ($proyecto->estado_proyecto === 'cancelado' || !empty($proyecto->cancelado_at)) {
            return back()->with('status', 'No se pueden subir archivos a un servicio cancelado.');
        }

        // Determinar o crear la ruta base de archivos para este proyecto específico
        if (empty($proyecto->ruta_carpeta_archivos)) {
            $ruta = 'proyectos/' . $proyecto->id;
            $proyecto->update(['ruta_carpeta_archivos' => $ruta]);
        } else {
            $ruta = $proyecto->ruta_carpeta_archivos;
        }

        // Subir el archivo (manteniendo nombre original o generando uno único seguro)
        $archivo = $request->file('archivo');
        $nombre_original = $archivo->getClientOriginalName();
        $nombre_archivo = Str::uuid()->toString() . '_' . $nombre_original;
        
        DB::transaction(function () use ($proyecto, $ruta, $archivo, $nombre_archivo) {
            // Guardamos el archivo físicamente en disco local
            $ruta_archivo = Storage::disk('local')->putFileAs($ruta, $archivo, $nombre_archivo);

            if (!$ruta_archivo) {
                abort(500, 'No se pudo guardar el archivo en el almacenamiento.');
            }

            $datosArchivo = [
                'id_proyecto' => $proyecto->id,
                'archivo' => $ruta_archivo,
            ];

            if (Schema::hasColumn('archivos_proyecto', 'id_usuario')) {
                $datosArchivo['id_usuario'] = auth()->id();
            }

            if (Schema::hasColumn('archivos_proyecto', 'fecha_subida')) {
                $datosArchivo['fecha_subida'] = now();
            }

            ArchivoProyecto::create($datosArchivo);
        });

        return back()->with('status', 'Archivo compartido correctamente en el encargo.');
    }

    public function download(Request $request, $id_proyecto)
    {
        $archivoId = $request->query('file');
        if (!$archivoId) abort(404);

        $proyecto = Proyecto::with('servicio')->findOrFail($id_proyecto);
        $usuario_actual = auth()->id();

        // Validar participación (Cliente contratante o Ingeniero ejecutor) o Admin
        $esCliente = ($proyecto->id_usuario === $usuario_actual);
        $esIngeniero = ($proyecto->servicio && $proyecto->servicio->id_usuario === $usuario_actual);

        if (!$esCliente && !$esIngeniero && !auth()->user()->esAdmin()) {
            abort(403, 'No estás autorizado para descargar archivos de este proyecto.');
        }

        $archivoProyecto = ArchivoProyecto::where('id', $archivoId)
            ->where('id_proyecto', $proyecto->id)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($archivoProyecto->archivo)) {
            abort(404, 'El archivo solicitado ya no existe en el disco.');
        }

        return Storage::disk('local')->download($archivoProyecto->archivo, basename($archivoProyecto->archivo));
    }
}
