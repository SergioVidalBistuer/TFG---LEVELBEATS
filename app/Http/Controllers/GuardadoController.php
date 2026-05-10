<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Guardado;

class GuardadoController extends Controller
{
    /* =================================================================
     * GET /usuario/guardados
     * Lista todos los guardados del usuario autenticado, agrupados por tipo.
     * ================================================================= */
    public function index()
    {
        $usuario = Auth::user();

        // Tipos que el morphMap reconoce. Cualquier otro se ignora de forma segura.
        $tiposValidos = array_keys(Guardado::tiposPermitidos());

        // Carga todos los guardados del usuario cuyo tipo esté en la lista blanca.
        $guardados = Guardado::where('id_usuario', $usuario->id)
            ->whereIn('guardable_type', $tiposValidos)   // descarta filas con tipo desconocido
            ->with('guardable')                          // eager-load polimórfico (usa morphMap)
            ->orderByDesc('fecha_guardado')
            ->get();

        // Separamos por tipo, descartando aquellos cuyo producto ya no exista en BD.
        $beats = $guardados
            ->filter(fn($g) => $g->guardable_type === 'beat' && $g->guardable !== null);

        $colecciones = $guardados
            ->filter(fn($g) => $g->guardable_type === 'coleccion' && $g->guardable !== null);

        $servicios = $guardados
            ->filter(fn($g) => $g->guardable_type === 'servicio' && $g->guardable !== null);

        // Carga relaciones adicionales necesarias para las cards de la vista.
        // loadMissing() es seguro aunque la relación ya esté cargada.
        foreach ($colecciones as $g) {
            $g->guardable->loadMissing('beats');
        }

        foreach ($servicios as $g) {
            $g->guardable->loadMissing('usuario');
        }

        return view('usuario.guardados.index', compact('beats', 'colecciones', 'servicios'));
    }

    /* =================================================================
     * POST /guardados/toggle
     * Guarda o quita un elemento dependiendo de si ya existe.
     * Body: tipo (beat|coleccion|servicio), id (int)
     * ================================================================= */
    public function toggle(Request $request)
    {
        $request->validate([
            'tipo' => ['required', 'string', 'in:beat,coleccion,servicio'],
            'id'   => ['required', 'integer', 'min:1'],
        ]);

        $tipo       = $request->input('tipo');
        $productoId = (int) $request->input('id');
        $usuario    = Auth::user();

        // Resolvemos la clase Eloquent real a través del morphMap
        // (Relation::getMorphedModel devuelve null si el tipo no está mapeado).
        $clase = Relation::getMorphedModel($tipo) ?? Guardado::clasePorTipo($tipo);

        if (!$clase) {
            return back()->with('status', 'Tipo de producto no válido.');
        }

        // Verificar que el producto existe en BD antes de guardarlo.
        $producto = $clase::find($productoId);
        if (!$producto) {
            return back()->with('status', 'El producto no existe.');
        }

        // Toggle: si ya existe lo eliminamos, si no existe lo creamos.
        $existente = Guardado::where('id_usuario', $usuario->id)
            ->where('guardable_type', $tipo)        // guardamos el string corto, no el FQCN
            ->where('guardable_id', $productoId)
            ->first();

        if ($existente) {
            $existente->delete();
            $mensaje = 'Eliminado de guardados.';
        } else {
            Guardado::create([
                'id_usuario'     => $usuario->id,
                'guardable_type' => $tipo,           // 'beat' | 'coleccion' | 'servicio'
                'guardable_id'   => $productoId,
                'fecha_guardado' => now(),
            ]);
            $mensaje = '¡Guardado correctamente!';
        }

        return back()->with('status', $mensaje);
    }

    /* =================================================================
     * POST /guardados/{tipo}/{id}/eliminar
     * Elimina directamente (útil desde la página de guardados).
     * No necesita resolver el modelo polimórfico, opera directo en BD.
     * ================================================================= */
    public function eliminar(string $tipo, int $id)
    {
        $usuario = Auth::user();

        // Whitelist de tipos: solo borramos si el tipo es conocido.
        if (!array_key_exists($tipo, Guardado::tiposPermitidos())) {
            return back()->with('status', 'Tipo no válido.');
        }

        // Borrado directo sin resolver el morphTo → no depende del morphMap.
        Guardado::where('id_usuario', $usuario->id)
            ->where('guardable_type', $tipo)
            ->where('guardable_id', $id)
            ->delete();

        return back()->with('status', 'Eliminado de guardados.');
    }
}
