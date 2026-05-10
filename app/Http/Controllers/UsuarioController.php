<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\CompraDetalle;
use App\Models\Proyecto;
use App\Models\Rol;
use App\Models\Usuario;
use App\Support\LicenciaCompra;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
    // PERFIL DEL USUARIO LOGUEADO
    public function profile()
    {
        // Eager-load incluye 'roles' para que las vistas puedan mostrar badges sin N+1
        $usuario = Usuario::with(['beats', 'colecciones.beats', 'comprasComoComprador', 'roles'])
            ->findOrFail(auth()->id());

        // ALGORITMO SELF-HEALING: Limpieza de duplicados históricos en Backend
        $subsHistoricas = $usuario->suscripciones()
            ->with(['planPorRol.rol'])
            ->where('estado_suscripcion', 'activa')
            ->orderByDesc('id')
            ->get();
            
        $rolesAuditados = [];
        foreach ($subsHistoricas as $sub) {
            if ($sub->planPorRol && $sub->planPorRol->rol) {
                $rol_nombre = $sub->planPorRol->rol->nombre_rol;
                if (in_array($rol_nombre, $rolesAuditados)) {
                    // Ya vimos una sub activa más reciente. Silenciamos histórico duplicado.
                    $sub->update(['estado_suscripcion' => 'expirada']);
                } else {
                    $rolesAuditados[] = $rol_nombre;
                }
            }
        }

        return view('usuario.profile', compact('usuario'));
    }

    // PERFIL PÚBLICO DE OTRO USUARIO
    public function publicProfile($id)
    {
        $usuario = Usuario::with(['beats', 'colecciones.beats', 'comprasComoComprador', 'roles'])
            ->findOrFail($id);

        return view('usuario.profile', compact('usuario'));
    }

    public function misProductos()
    {
        $usuario = Usuario::with([
            'comprasComoComprador.beats',
            'comprasComoComprador.colecciones.beats',
            'comprasComoComprador.servicios.usuario',
            'comprasComoComprador.detalles.licencia',
        ])->findOrFail(auth()->id());

        $compras = $usuario->comprasComoComprador;
        $proyectosPorCompra = Proyecto::with(['servicio.usuario'])
            ->whereIn('id_compra', $compras->pluck('id')->filter()->unique()->values())
            ->get()
            ->keyBy('id_compra');

        $serviciosContratados = $compras
            ->flatMap(function ($compra) use ($proyectosPorCompra) {
                return $compra->servicios->map(function ($servicio) use ($compra, $proyectosPorCompra) {
                    return [
                        'servicio' => $servicio,
                        'compra' => $compra,
                        'proyecto' => $proyectosPorCompra->get($compra->id),
                    ];
                });
            })
            ->values();

        $detallesCompra = $compras
            ->flatMap(fn ($compra) => $compra->detalles)
            ->sortByDesc('fecha')
            ->values();

        $detallesBeat = $detallesCompra
            ->where('tipo_producto', 'beat')
            ->values();

        $detallesColeccion = $detallesCompra
            ->where('tipo_producto', 'coleccion')
            ->values();

        $beatsDetalle = Beat::whereIn('id', $detallesBeat->pluck('id_producto')->unique()->values())
            ->get()
            ->keyBy('id');

        $coleccionesDetalle = Coleccion::with('beats')
            ->whereIn('id', $detallesColeccion->pluck('id_producto')->unique()->values())
            ->get()
            ->keyBy('id');

        $bibliotecaBeats = collect();

        foreach ($detallesBeat as $detalle) {
            $beat = $beatsDetalle->get($detalle->id_producto);
            if ($beat && !$bibliotecaBeats->has($beat->id)) {
                $bibliotecaBeats->put($beat->id, [
                    'beat' => $beat,
                    'detalle' => $detalle,
                    'origen' => 'Compra directa',
                ]);
            }
        }

        foreach ($detallesColeccion as $detalle) {
            $coleccion = $coleccionesDetalle->get($detalle->id_producto);
            if (!$coleccion) {
                continue;
            }

            foreach ($coleccion->beats as $beat) {
                if (!$bibliotecaBeats->has($beat->id)) {
                    $bibliotecaBeats->put($beat->id, [
                        'beat' => $beat,
                        'detalle' => $detalle,
                        'origen' => 'Incluido en ' . $coleccion->titulo_coleccion,
                    ]);
                }
            }
        }

        $beatsDirectos = $compras
            ->flatMap(fn ($compra) => $compra->beats);

        $colecciones = $compras
            ->flatMap(fn ($compra) => $compra->colecciones)
            ->unique('id')
            ->values();

        $beatsDeColecciones = $colecciones
            ->flatMap(fn ($coleccion) => $coleccion->beats);

        $beatsComprados = $beatsDirectos
            ->merge($beatsDeColecciones)
            ->unique('id')
            ->values();

        foreach ($beatsComprados as $beat) {
            if (!$bibliotecaBeats->has($beat->id)) {
                $bibliotecaBeats->put($beat->id, [
                    'beat' => $beat,
                    'detalle' => null,
                    'origen' => 'Compra anterior',
                ]);
            }
        }

        $bibliotecaBeats = $bibliotecaBeats->values();
        $coleccionesConDetalle = $detallesColeccion->pluck('id_producto')->unique();
        $coleccionesLegacy = $colecciones
            ->reject(fn ($coleccion) => $coleccionesConDetalle->contains($coleccion->id))
            ->values();

        return view('usuario.productos.index', compact(
            'beatsComprados',
            'colecciones',
            'detallesCompra',
            'detallesBeat',
            'detallesColeccion',
            'beatsDetalle',
            'coleccionesDetalle',
            'bibliotecaBeats',
            'coleccionesLegacy',
            'serviciosContratados'
        ));
    }

    public function settings()
    {
        $usuario = Usuario::findOrFail(auth()->id());

        return view('usuario.settings', compact('usuario'));
    }

    public function updateSettingsProfile(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());

        $request->validate([
            'nombre_usuario' => 'required|string|max:80',
            'direccion_correo' => 'required|email|max:120|unique:usuario,direccion_correo,' . $usuario->id,
            'descripcion_perfil' => 'nullable|string',
            'calle' => 'nullable|string|max:120',
            'localidad' => 'nullable|string|max:120',
            'provincia' => 'nullable|string|max:120',
            'pais' => 'nullable|string|max:120',
            'codigo_postal' => 'nullable|string|max:20',
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'nombre_usuario.max' => 'El nombre de usuario no puede superar los :max caracteres.',
            'direccion_correo.required' => 'El correo electrónico es obligatorio.',
            'direccion_correo.email' => 'Introduce un correo electrónico válido.',
            'direccion_correo.max' => 'El correo electrónico no puede superar los :max caracteres.',
            'direccion_correo.unique' => 'Ese correo electrónico ya está registrado.',
            'calle.max' => 'La calle no puede superar los :max caracteres.',
            'localidad.max' => 'La localidad no puede superar los :max caracteres.',
            'provincia.max' => 'La provincia no puede superar los :max caracteres.',
            'pais.max' => 'El país no puede superar los :max caracteres.',
            'codigo_postal.max' => 'El código postal no puede superar los :max caracteres.',
        ]);

        $usuario->update($request->only([
            'nombre_usuario',
            'direccion_correo',
            'descripcion_perfil',
            'calle',
            'localidad',
            'provincia',
            'pais',
            'codigo_postal',
        ]));

        session(['usuario_nombre' => $usuario->nombre_usuario]);

        return back()->with('status', 'Datos de la cuenta actualizados correctamente.');
    }

    public function updateSettingsPhoto(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());

        $request->validate([
            'foto_perfil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_perfil.required' => 'Selecciona una imagen para actualizar tu foto de perfil.',
            'foto_perfil.image' => 'El archivo debe ser una imagen válida.',
            'foto_perfil.mimes' => 'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',
            'foto_perfil.max' => 'La imagen no puede superar los 2 MB.',
        ]);

        if ($usuario->url_foto_perfil && str_starts_with($usuario->url_foto_perfil, 'storage/')) {
            Storage::disk('public')->delete(substr($usuario->url_foto_perfil, strlen('storage/')));
        }

        $ruta = $request->file('foto_perfil')->store('usuarios/perfiles', 'public');
        $usuario->url_foto_perfil = 'storage/' . $ruta;
        $usuario->save();

        return back()->with('status', 'Foto de perfil actualizada correctamente.');
    }

    public function updateSettingsPassword(Request $request)
    {
        $usuario = Usuario::findOrFail(auth()->id());

        $request->validate([
            'contrasena_actual' => 'required|string',
            'nueva_contrasena' => 'required|string|min:8|confirmed',
        ], [
            'contrasena_actual.required' => 'Introduce tu contraseña actual.',
            'nueva_contrasena.required' => 'Introduce una nueva contraseña.',
            'nueva_contrasena.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'nueva_contrasena.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        if (!Hash::check($request->input('contrasena_actual'), $usuario->contrasena)) {
            return back()->withErrors(['contrasena_actual' => 'La contraseña actual no es correcta.']);
        }

        $usuario->contrasena = Hash::make($request->input('nueva_contrasena'));
        $usuario->save();

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }

    public function descargarBeatComprado($id)
    {
        $beat = Beat::findOrFail($id);

        if (!$this->usuarioTieneBeatComprado(auth()->id(), $beat->id)) {
            abort(403, 'No tienes permiso para descargar este beat.');
        }

        if (empty($beat->url_archivo_final)) {
            abort(404, 'Este beat todavía no tiene archivo final disponible.');
        }

        $ruta = ltrim($beat->url_archivo_final, '/');
        $nombreDescarga = basename($ruta);

        if (Storage::disk('local')->exists($ruta)) {
            return Storage::disk('local')->download($ruta, $nombreDescarga);
        }

        if (Storage::disk('public')->exists($ruta)) {
            return Storage::disk('public')->download($ruta, $nombreDescarga);
        }

        $rutaPublica = public_path($ruta);
        if (is_file($rutaPublica)) {
            return response()->download($rutaPublica, $nombreDescarga);
        }

        abort(404, 'El archivo final del beat no existe en el almacenamiento.');
    }

    public function verLicenciaComprada($detalleId)
    {
        return $this->emitirLicenciaPdf($detalleId, false);
    }

    public function descargarLicenciaComprada($detalleId)
    {
        return $this->emitirLicenciaPdf($detalleId, true);
    }

    private function emitirLicenciaPdf($detalleId, bool $forzarDescarga)
    {
        $detalle = CompraDetalle::with(['compra.comprador', 'compra.factura', 'licencia'])
            ->findOrFail($detalleId);

        if (!auth()->user()->esAdmin() && $detalle->compra->id_usuario_comprador !== auth()->id()) {
            abort(403, 'No tienes permiso para descargar esta licencia.');
        }

        if (!class_exists(Dompdf::class)) {
            abort(500, 'DomPDF no está instalado. Ejecuta: composer require dompdf/dompdf');
        }

        $producto = $detalle->tipo_producto === 'beat'
            ? Beat::with('usuario')->find($detalle->id_producto)
            : Coleccion::with(['usuario', 'beats'])->find($detalle->id_producto);

        $licenciante = $producto?->usuario;
        $comprador = $detalle->compra->comprador;
        $spec = LicenciaCompra::spec($detalle->licencia);
        $tipoLicencia = $this->tipoLicenciaDetalle($detalle);
        $beatsIncluidos = $detalle->tipo_producto === 'coleccion' && $producto
            ? $producto->beats
            : collect();
        $logoDataUri = $this->logoPdfDataUri();

        $html = view('pdf.licencia', compact(
            'detalle',
            'producto',
            'licenciante',
            'comprador',
            'spec',
            'tipoLicencia',
            'beatsIncluidos',
            'logoDataUri'
        ))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombre = 'licencia-levelbeats-' . $detalle->id . '-' . $tipoLicencia . '.pdf';

        $dompdf->stream($nombre, [
            'Attachment' => $forzarDescarga,
        ]);

        exit;
    }

    private function tipoLicenciaDetalle(CompraDetalle $detalle): string
    {
        if ($detalle->licencia?->tipo_licencia) {
            return $detalle->licencia->tipo_licencia;
        }

        $nombre = mb_strtolower($detalle->nombre_licencia_snapshot ?? '');

        if (str_contains($nombre, 'exclusiva')) {
            return 'exclusiva';
        }

        if (str_contains($nombre, 'premium')) {
            return 'premium';
        }

        return 'basica';
    }

    private function logoPdfDataUri(): ?string
    {
        $path = public_path('media/img/LB-04-pdf.png');

        if (!is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    private function usuarioTieneBeatComprado(int $idUsuario, int $idBeat): bool
    {
        return Usuario::where('id', $idUsuario)
            ->whereHas('comprasComoComprador.beats', function ($query) use ($idBeat) {
                $query->where('beat.id', $idBeat);
            })
            ->orWhere(function ($query) use ($idUsuario, $idBeat) {
                $query->where('id', $idUsuario)
                    ->whereHas('comprasComoComprador.colecciones.beats', function ($beatQuery) use ($idBeat) {
                        $beatQuery->where('beat.id', $idBeat);
                    });
            })
            ->exists();
    }

    // LISTADO (solo admin por middleware)
    public function index()
    {
        // 'roles' eager-loaded para evitar N+1 al mostrar rol de cada usuario en tabla
        $usuarios = Usuario::with('roles')->orderBy('id', 'desc')->get();

        return view('usuario.index', compact('usuarios'));
    }

    // FORMULARIO CREAR
    public function create()
    {
        return view('usuario.create');
    }

    // GUARDAR
    public function save(Request $request)
    {
        $request->validate([
            'nombre_usuario'   => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo',
            'contrasena'       => 'required|min:6',
            'rol'              => 'nullable|in:usuario,admin', // nombre_rol válido en la tabla rol
        ]);

        $usuario = Usuario::create([
            'nombre_usuario'          => $request->nombre_usuario,
            'direccion_correo'        => $request->direccion_correo,
            'contrasena'              => Hash::make($request->contrasena),
            // 'rol' eliminado: se asigna vía tabla pivote usuario_rol, no como columna
            'verificacion_completada' => 1,
            'descripcion_perfil'      => $request->descripcion_perfil,
            'calle'                   => $request->calle,
            'localidad'               => $request->localidad,
            'provincia'               => $request->provincia,
            'pais'                    => $request->pais,
            'codigo_postal'           => $request->codigo_postal,
            'fecha_registro'          => now(),
        ]);

        // Admin puede asignar rol al crear; el resto recibe 'usuario' por defecto
        $rolNombre = auth()->user()->esAdmin()
            ? $request->input('rol', 'usuario')
            : 'usuario';

        $rol = Rol::where('nombre_rol', $rolNombre)->first();
        if ($rol) {
            $usuario->roles()->attach($rol->id, ['rol_activo' => 1]);
        }

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario creado correctamente');
    }

    // FORMULARIO EDITAR
    public function edit($id)
    {
        // Cargar roles para que edit.blade.php pueda detectar el rol base actual
        $usuario = Usuario::with('roles')->findOrFail($id);

        return view('usuario.edit', compact('usuario'));
    }

    // ACTUALIZAR
    public function update(Request $request)
    {
        $request->validate([
            'nombre_usuario'   => 'required|max:80',
            'direccion_correo' => 'required|email|unique:usuario,direccion_correo,' . $request->id,
            'rol'              => 'nullable|in:usuario,admin',
        ]);

        $usuario = Usuario::findOrFail($request->id);

        $usuario->nombre_usuario     = $request->nombre_usuario;
        $usuario->direccion_correo   = $request->direccion_correo;
        $usuario->descripcion_perfil = $request->descripcion_perfil;
        $usuario->calle              = $request->calle;
        $usuario->localidad          = $request->localidad;
        $usuario->provincia          = $request->provincia;
        $usuario->pais               = $request->pais;
        $usuario->codigo_postal      = $request->codigo_postal;

        $usuario->save();

        // SOLO ADMIN puede cambiar el rol base del usuario
        if (auth()->user()->esAdmin() && $request->filled('rol')) {
            $rolNuevo = Rol::where('nombre_rol', $request->input('rol'))->first();

            if ($rolNuevo) {
                // Detach SOLO los roles base (admin/usuario), preservando
                // roles especializados como productor e ingeniero
                $rolesBase = Rol::whereIn('nombre_rol', ['admin', 'usuario'])->pluck('id');
                $usuario->roles()->detach($rolesBase);
                $usuario->roles()->attach($rolNuevo->id, ['rol_activo' => 1]);
            }
        }

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario actualizado correctamente');
    }

    // ELIMINAR
    public function delete($id)
    {
        $usuario = Usuario::findOrFail($id);

        // Protección: no puede borrarse a sí mismo
        if (auth()->id() == $usuario->id) {
            return back()->with('status', 'No puedes eliminar tu propio usuario');
        }

        $usuario->delete();

        return redirect()->route('usuario.index')
            ->with('status', 'Usuario eliminado correctamente');
    }
}
