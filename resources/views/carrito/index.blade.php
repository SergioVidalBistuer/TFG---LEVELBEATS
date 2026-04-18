@extends('layouts.master')

@section('title', 'Carrito')

@section('content')

    <h1>Carrito</h1>

    @if(empty($cart['beats']) && empty($cart['colecciones']))
        <div class="panel" style="text-align: center; padding: 40px;">
            <p style="font-size: 18px; color: rgba(255,255,255,0.7);">El carrito está vacío.</p>
            <a href="{{ route('beat.index') }}" class="btn btn--primary" style="margin-top: 20px;">Explorar Beats</a>
        </div>
    @else

        <form action="{{ route('carrito.update') }}" method="POST">
            @csrf

            {{-- ================= BEATS ================= --}}
            @if($beats->count())
                <h2 style="margin-top: 0; font-size: 20px; color: var(--text);">Beats</h2>
                <div style="overflow-x: auto;">
                    <table class="table-lb">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($beats as $beat)
                            <tr>
                                <td>{{ $beat->titulo_beat }}</td>
                                <td>{{ $beat->precio_base_licencia }} €</td>
                                <td>
                                    <input type="number"
                                           name="beats[{{ $beat->id }}]"
                                           value="{{ $cart['beats'][$beat->id] }}"
                                           min="0"
                                           class="input--lb"
                                           style="width: 70px;">
                                </td>
                                <td style="font-weight: 600;">
                                    {{ $beat->precio_base_licencia * $cart['beats'][$beat->id] }} €
                                </td>
                                <td>
                                    <a href="{{ route('carrito.remove', ['type'=>'beat','id'=>$beat->id]) }}" 
                                       class="btn btn--icon" style="color: #ff4d4d;" title="Eliminar">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif


            {{-- ================= COLECCIONES ================= --}}
            @if($colecciones->count())
                <h2 style="margin-top: 32px; font-size: 20px; color: var(--text);">Colecciones</h2>
                <div style="overflow-x: auto;">
                    <table class="table-lb">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($colecciones as $coleccion)
                            <tr>
                                <td>{{ $coleccion->titulo_coleccion }}</td>
                                <td>{{ $coleccion->precio }} €</td>
                                <td>
                                    <input type="number"
                                           name="colecciones[{{ $coleccion->id }}]"
                                           value="{{ $cart['colecciones'][$coleccion->id] }}"
                                           min="0"
                                           class="input--lb"
                                           style="width: 70px;">
                                </td>
                                <td style="font-weight: 600;">
                                    {{ $coleccion->precio * $cart['colecciones'][$coleccion->id] }} €
                                </td>
                                <td>
                                    <a href="{{ route('carrito.remove', ['type'=>'coleccion','id'=>$coleccion->id]) }}" 
                                       class="btn btn--icon" style="color: #ff4d4d;" title="Eliminar">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div style="text-align: right; margin-bottom: 32px;">
                <button type="submit" class="btn btn--ghost">Actualizar carrito</button>
            </div>
        </form>

        <div class="cart-summary">
            <h2>Total: {{ $total }} €</h2>

            <div class="d-flex align-items-center justify-content-end gap-3 flex-wrap">
                <a href="{{ route('carrito.clear') }}" class="btn btn--ghost" style="color: #ff4d4d; border-color: rgba(255,77,77,0.3);">
                    Vaciar carrito
                </a>
                
                @if($total > 0)
                    <form action="{{ route('compra.checkout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn--primary" style="padding-left: 32px; padding-right: 32px; font-size: 16px;">Finalizar compra</button>
                    </form>
                @endif
            </div>
        </div>

    @endif
@endsection
