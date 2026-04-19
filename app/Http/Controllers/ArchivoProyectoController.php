<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Storage;

class ArchivoProyectoController extends Controller
{
    public function upload(Request $request, $id_proyecto)
    {
        $request->validate([
            'archivo' => 'required|file|max:51200' // Límite de 50MB (ajustable)
        ]);

        $proyecto = Proyecto::with('servicio')->findOrFail($id_proyecto);
        $usuario_actual = auth()->id();

        // Validar participación (Cliente contratante o Ingeniero ejecutor) o Admin
        $esCliente = ($proyecto->id_usuario === $usuario_actual);
        $esIngeniero = ($proyecto->servicio && $proyecto->servicio->id_usuario === $usuario_actual);

        if (!$esCliente && !$esIngeniero && !auth()->user()->esAdmin()) {
            abort(403, 'No estás autorizado para subir archivos a este proyecto.');
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
        
        // Guardamos el archivo físicamente en disco local
        Storage::disk('local')->putFileAs($ruta, $archivo, $nombre_original);

        return back()->with('status', 'Archivo subido y asociado correctamente al proyecto.');
    }

    public function download(Request $request, $id_proyecto)
    {
        $archivoPath = $request->query('file');
        if (!$archivoPath) abort(404);

        $proyecto = Proyecto::with('servicio')->findOrFail($id_proyecto);
        $usuario_actual = auth()->id();

        // Validar participación (Cliente contratante o Ingeniero ejecutor) o Admin
        $esCliente = ($proyecto->id_usuario === $usuario_actual);
        $esIngeniero = ($proyecto->servicio && $proyecto->servicio->id_usuario === $usuario_actual);

        if (!$esCliente && !$esIngeniero && !auth()->user()->esAdmin()) {
            abort(403, 'No estás autorizado para descargar archivos de este proyecto.');
        }

        // Seguridad estricta: Verificar que la ruta solicitada empiece lógicamente por la carpeta del proyecto
        // Sirve para prevenir path traversal via 'file=../../otro_directorio'
        if (strpos($archivoPath, $proyecto->ruta_carpeta_archivos) !== 0) {
            abort(403, 'Archivo no válido o desvinculado de este proyecto.');
        }

        if (!Storage::disk('local')->exists($archivoPath)) {
            abort(404, 'El archivo solicitado ya no existe en el disco.');
        }

        return Storage::disk('local')->download($archivoPath);
    }
}
