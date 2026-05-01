<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use App\Models\Coleccion;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Mostrar carrito
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $cart = session()->get('cart', [
            'beats' => []
        ]);

        $beats = collect();

        if (!empty($cart['beats'])) {
            $beats = Beat::whereIn('id', array_keys($cart['beats']))->get();
        }

        $total = $beats->sum('precio_base_licencia');

        return view('carrito.index', compact('cart', 'beats', 'total'));
    }

    /*
    |--------------------------------------------------------------------------
    | Añadir Beat
    |--------------------------------------------------------------------------
    */
    public function addBeat(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:beat,id',
        ]);

        $cart = session()->get('cart', [
            'beats' => []
        ]);

        // Guardamos el ID del beat. No hay "cantidades", un beat se compra 1 vez.
        $cart['beats'][$data['id']] = true;

        session()->put('cart', $cart);

        return redirect()
            ->back()
            ->with('status', 'Beat añadido al carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar elemento
    |--------------------------------------------------------------------------
    */
    public function remove(string $type, int $id)
    {
        $cart = session()->get('cart', [
            'beats' => []
        ]);

        if ($type === 'beat') {
            unset($cart['beats'][$id]);
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
    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('carrito.index')
            ->with('status', 'Carrito vaciado');
    }
}
