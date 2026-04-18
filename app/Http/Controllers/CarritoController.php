<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use Illuminate\Http\Request;

/**
 * Class CarritoController
 * 
 * Controlador para gestionar el carrito de compras, permitiendo añadir,
 * actualizar y eliminar beats y colecciones.
 */
class CarritoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Mostrar carrito
    |--------------------------------------------------------------------------
    */
    /**
     * Muestra el contenido actual del carrito de compras.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = session()->get('cart', [
            'beats' => [],
            'colecciones' => []
        ]);

        $beats = collect();
        $colecciones = collect();

        if (!empty($cart['beats'])) {
            $beats = Beat::whereIn('id', array_keys($cart['beats']))->get();
        }

        if (!empty($cart['colecciones'])) {
            $colecciones = Coleccion::whereIn('id', array_keys($cart['colecciones']))->get();
        }

        $totalBeats = $beats->sum(function ($beat) use ($cart) {
            return (float)$beat->precio_base_licencia * (int)$cart['beats'][$beat->id];
        });

        $totalColecciones = $colecciones->sum(function ($coleccion) use ($cart) {
            return (float)$coleccion->precio * (int)$cart['colecciones'][$coleccion->id];
        });

        $total = $totalBeats + $totalColecciones;

        return view('carrito.index', compact(
            'cart',
            'beats',
            'colecciones',
            'total'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Añadir Beat
    |--------------------------------------------------------------------------
    */
    /**
     * Añade un beat al carrito de compras.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con el id del beat y cantidad.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación falla (ej. si el beat no existe).
     */
    public function addBeat(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:beat,id',
            'cantidad' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', [
            'beats' => [],
            'colecciones' => []
        ]);

        $cart['beats'][$data['id']] =
            ($cart['beats'][$data['id']] ?? 0) + $data['cantidad'];

        session()->put('cart', $cart);

        return redirect()
            ->back()
            ->with('status', 'Beat añadido al carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Añadir Colección
    |--------------------------------------------------------------------------
    */
    /**
     * Añade una colección al carrito de compras.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP con el id de la colección y cantidad.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException Si la validación falla (ej. si la colección no existe).
     */
    public function addColeccion(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:coleccion,id',
            'cantidad' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', [
            'beats' => [],
            'colecciones' => []
        ]);

        $cart['colecciones'][$data['id']] =
            ($cart['colecciones'][$data['id']] ?? 0) + $data['cantidad'];

        session()->put('cart', $cart);

        return redirect()
            ->back()
            ->with('status', 'Colección añadida al carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar cantidades
    |--------------------------------------------------------------------------
    */
    /**
     * Actualiza las cantidades de los elementos en el carrito.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $cart = session()->get('cart', [
            'beats' => [],
            'colecciones' => []
        ]);

        foreach ($request->input('beats', []) as $id => $cantidad) {
            $cantidad = (int)$cantidad;

            if ($cantidad <= 0) {
                unset($cart['beats'][$id]);
            } else {
                $cart['beats'][$id] = $cantidad;
            }
        }

        foreach ($request->input('colecciones', []) as $id => $cantidad) {
            $cantidad = (int)$cantidad;

            if ($cantidad <= 0) {
                unset($cart['colecciones'][$id]);
            } else {
                $cart['colecciones'][$id] = $cantidad;
            }
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Carrito actualizado');
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar elemento
    |--------------------------------------------------------------------------
    */
    /**
     * Elimina un elemento específico del carrito.
     *
     * @param  string  $type
     * @param  int     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(string $type, int $id)
    {
        $cart = session()->get('cart', [
            'beats' => [],
            'colecciones' => []
        ]);

        if ($type === 'beat') {
            unset($cart['beats'][$id]);
        }

        if ($type === 'coleccion') {
            unset($cart['colecciones'][$id]);
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Elemento eliminado');
    }

    /*
    |--------------------------------------------------------------------------
    | Vaciar carrito
    |--------------------------------------------------------------------------
    */
    /**
     * Vacía completamente el carrito de compras.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Carrito vaciado');
    }
}
