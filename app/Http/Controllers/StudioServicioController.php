<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudioServicioController extends Controller
{
    /**
     * Asegura que el creador solo gestione sus propios servicios.
     */
    private function canManage(Servicio $servicio): bool
    {
        return $servicio->id_usuario === auth()->id();
    }

    private function servicioTienePortada(): bool
    {
        return filled(Servicio::portadaColumn());
    }

    private function guardarPortada(Request $request): ?string
    {
        if (!$request->hasFile('portada_servicio')) {
            return null;
        }

        $archivo = $request->file('portada_servicio');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre = Str::uuid()->toString() . '.' . $extension;
        $ruta = $archivo->storeAs('servicios/covers/' . auth()->id(), $nombre, 'public');

        return 'storage/' . $ruta;
    }

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if (!$ruta || !str_starts_with($ruta, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($ruta, strlen('storage/')));
    }

    public function index()
    {
        abort_unless(auth()->user()->tieneRol('ingeniero'), 403, 'Acceso exclusivo para Ingenieros.');
        
        // El usuario ve SOLO sus propios servicios ofertados:
        $servicios = Servicio::where('id_usuario', auth()->id())->orderBy('id', 'desc')->get();
        return view('studio.servicios.index', compact('servicios'));
    }

    public function create()
    {
        abort_unless(auth()->user()->tieneRol('ingeniero'), 403, 'Acceso exclusivo para Ingenieros.');

        $usuario = auth()->user();
        {
            $rolIngeniero = \App\Models\Rol::where('nombre_rol', 'ingeniero')->first();
            $sub = $usuario->suscripciones()->with('planPorRol')
                           ->where('id_rol', $rolIngeniero->id)
                           ->where('estado_suscripcion', 'activa')
                           ->latest('fecha_inicio')->first();
            
            if (!$sub) {
                return redirect()->route('onboarding.planes', ['rol' => 'ingeniero'])
                                 ->with('status', 'Aviso: Necesitas validar un Plan para ofertar Servicios técnicos.');
            }

            $limite = $sub->planPorRol->encargos_max_ingeniero;
            $serviciosActuales = Servicio::where('id_usuario', $usuario->id)->count();

            if ($serviciosActuales >= $limite && $limite < 90) {
                return redirect()->route('studio.servicios.index')
                                 ->with('status', "Carga máxima ($limite) alcanzada. Actualiza tu plan para ampliar tu Studio.");
            }
        }

        return view('studio.servicios.form');
    }

    public function save(Request $request)
    {
        $usuario = auth()->user();
        $rolIngeniero = \App\Models\Rol::where('nombre_rol', 'ingeniero')->first();
        $sub = $usuario->suscripciones()->with('planPorRol')
                       ->where('id_rol', $rolIngeniero->id)
                       ->where('estado_suscripcion', 'activa')
                       ->latest('fecha_inicio')->first();

        $limite = $sub ? $sub->planPorRol->encargos_max_ingeniero : 0;
        $serviciosActuales = Servicio::where('id_usuario', $usuario->id)->count();

        if (!$sub || ($serviciosActuales >= $limite && $limite < 90)) {
            return redirect()->route('studio.servicios.index')->with('status', 'Plan insuficiente.');
        }

        $request->validate([
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
            'portada_servicio'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'portada_servicio.image' => 'La portada debe ser una imagen válida.',
            'portada_servicio.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_servicio.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $datos = [
            'id_usuario'           => auth()->id(),
            'titulo_servicio'      => $request->titulo_servicio,
            'tipo_servicio'        => $request->tipo_servicio,
            'descripcion_servicio' => $request->descripcion_servicio,
            'precio_servicio'      => $request->precio_servicio,
            'plazo_entrega_dias'   => $request->plazo_entrega_dias,
            'numero_revisiones'    => $request->numero_revisiones,
            'url_portafolio'       => $request->url_portafolio,
            'servicio_activo'      => $request->has('servicio_activo'),
        ];

        if ($this->servicioTienePortada() && $request->hasFile('portada_servicio')) {
            $datos[Servicio::portadaColumn()] = $this->guardarPortada($request);
        }

        Servicio::create($datos);

        return redirect()->route('studio.servicios.index')->with('status', 'Servicio subido con éxito a tu catálogo técnico.');
    }

    public function edit($id)
    {
        $servicio = Servicio::findOrFail($id);
        if (!$this->canManage($servicio)) {
            abort(403, 'Acceso denegado a este servicio.');
        }

        return view('studio.servicios.form', compact('servicio'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                   => 'required|integer',
            'titulo_servicio'      => 'required|string|max:140',
            'tipo_servicio'        => 'required|in:mezcla,master,otro',
            'descripcion_servicio' => 'nullable|string|max:500',
            'precio_servicio'      => 'required|numeric',
            'plazo_entrega_dias'   => 'nullable|integer|min:1',
            'numero_revisiones'    => 'nullable|integer|min:0',
            'url_portafolio'       => 'nullable|url|max:255',
            'portada_servicio'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'portada_servicio.image' => 'La portada debe ser una imagen válida.',
            'portada_servicio.mimes' => 'La portada debe estar en formato JPG, PNG o WEBP.',
            'portada_servicio.max' => 'La portada no puede superar los 5 MB.',
        ]);

        $servicio = Servicio::findOrFail($request->id);
        if (!$this->canManage($servicio)) {
            abort(403);
        }

        $datos = [
            'titulo_servicio'      => $request->titulo_servicio,
            'tipo_servicio'        => $request->tipo_servicio,
            'descripcion_servicio' => $request->descripcion_servicio,
            'precio_servicio'      => $request->precio_servicio,
            'plazo_entrega_dias'   => $request->plazo_entrega_dias,
            'numero_revisiones'    => $request->numero_revisiones,
            'url_portafolio'       => $request->url_portafolio,
            'servicio_activo'      => $request->has('servicio_activo'),
        ];

        if ($this->servicioTienePortada() && $request->hasFile('portada_servicio')) {
            $rutaAnterior = $servicio->portada_url;
            $datos[Servicio::portadaColumn()] = $this->guardarPortada($request);
            $this->eliminarArchivoPublico($rutaAnterior);
        }

        $servicio->update($datos);

        return redirect()->route('studio.servicios.index')->with('status', 'Servicio actualizado con éxito.');
    }

    public function delete($id)
    {
        $servicio = Servicio::findOrFail($id);
        if (!$this->canManage($servicio)) {
            abort(403);
        }

        $servicio->delete();

        return redirect()->route('studio.servicios.index')->with('status', 'Servicio retirado de tu catálogo.');
    }
}
