<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use App\Models\Licencia;
use App\Support\CarritoCompra;
use App\Support\LicenciaCompra;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        $cart = CarritoCompra::normalizar(session()->get('cart'));
        session()->put('cart', $cart);

        $items = CarritoCompra::items($cart);
        $beats = $items['beats'];
        $colecciones = $items['colecciones'];
        $servicios = $items['servicios'];
        $total = $items['total'];

        return view('carrito.index', compact('cart', 'beats', 'colecciones', 'servicios', 'total'));
    }

    public function addBeat(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:beat,id',
            'licencia_id' => 'nullable|integer|exists:licencia,id',
        ], [
            'id.required' => 'No se ha podido identificar el beat.',
            'id.exists' => 'El beat seleccionado no existe.',
            'licencia_id.exists' => 'La licencia seleccionada no existe.',
        ]);

        $beat = Beat::publicados()->findOrFail($data['id']);
        $licencia = $this->licenciaSeleccionada($data['licencia_id'] ?? null);

        if ($beat->id_usuario === auth()->id()) {
            return back()->with('status', 'No puedes comprar tu propio beat.');
        }

        if ($licencia->tipo_licencia === 'exclusiva' && LicenciaCompra::exclusivaVendida('beat', $beat->id)) {
            return back()->with('status', 'La licencia exclusiva de este beat ya está vendida.');
        }

        $cart = CarritoCompra::agregarBeat(
            session()->get('cart', CarritoCompra::vacio()),
            $beat->id,
            $licencia->id
        );

        session()->put('cart', $cart);

        return back()->with('status', 'Beat añadido al carrito con ' . $licencia->nombre_licencia . '.');
    }

    public function addColeccion(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:coleccion,id',
            'licencia_id' => 'nullable|integer|exists:licencia,id',
        ], [
            'id.required' => 'No se ha podido identificar la colección.',
            'id.exists' => 'La colección seleccionada no existe.',
            'licencia_id.exists' => 'La licencia seleccionada no existe.',
        ]);

        $coleccion = Coleccion::publicadas()->findOrFail($data['id']);
        $licencia = $this->licenciaSeleccionada($data['licencia_id'] ?? null);

        if ($coleccion->id_usuario === auth()->id()) {
            return back()->with('status', 'No puedes comprar tu propia colección.');
        }

        if ($licencia->tipo_licencia === 'exclusiva' && LicenciaCompra::exclusivaVendida('coleccion', $coleccion->id)) {
            return back()->with('status', 'La licencia exclusiva de esta colección ya está vendida.');
        }

        $cart = CarritoCompra::agregarColeccion(
            session()->get('cart', CarritoCompra::vacio()),
            $coleccion->id,
            $licencia->id
        );

        session()->put('cart', $cart);

        return back()->with('status', 'Colección añadida al carrito con ' . $licencia->nombre_licencia . '.');
    }

    public function remove(string $type, string $id)
    {
        $cart = CarritoCompra::quitar(session()->get('cart', CarritoCompra::vacio()), $type, $id);
        session()->put('cart', $cart);

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Elemento eliminado');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Carrito vaciado');
    }

    private function licenciaSeleccionada(?int $licenciaId): Licencia
    {
        if (!$licenciaId) {
            $licencia = LicenciaCompra::licenciaBasica();
        } else {
            $licencia = Licencia::whereIn('tipo_licencia', LicenciaCompra::tiposPermitidos())
                ->find($licenciaId);
        }

        abort_unless($licencia, 422, 'No hay una licencia válida configurada para esta compra.');

        return $licencia;
    }
}
