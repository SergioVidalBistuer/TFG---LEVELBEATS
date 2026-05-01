@extends('layouts.master')

@section('title', 'Mis Compras')

@section('content')

    @if(auth()->user()->esAdmin())
        <div style="margin-bottom: 24px;">
            <a href="{{ route('admin.dashboard.index') }}"
                style="color: #ff5252; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px;">
                &larr; Volver al Dashboard Root
            </a>
            <h1>Pedidos Usuario (Panel Admin)</h1>
            <p style="color: rgba(255,255,255,0.6);">Historial de todas las transacciones realizadas en LevelBeat.</p>
        </div>
    @else
        <h1>Mis Compras</h1>
    @endif

    @if($compras->isEmpty())
        <div class="panel" style="text-align: center; padding: 40px;">
            <p style="font-size: 18px; color: rgba(255,255,255,0.7);">No tienes compras todavía.</p>
            <a href="{{ route('beat.index') }}" class="btn btn--primary" style="margin-top: 20px;">Explorar Beats</a>
        </div>
    @else

        <div style="overflow-x: auto;">
            <table class="table-lb">
                <thead>
                    <tr>
                        <th>Nº</th>
                        @if(auth()->user()->esAdmin())
                            <th>Comprador</th>
                        @endif
                        <th>Total</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                        <tr>
                            <td>#{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}</td>
                            @if(auth()->user()->esAdmin())
                                <td style="color: #00d4ff;">{{ $compra->comprador->nombre_usuario ?? 'Desconocido' }}</td>
                            @endif
                            <td style="font-weight: 600;">{{ $compra->importe_total }} €</td>
                            <td>{{ ucfirst($compra->metodo_de_pago) }}</td>
                            <td>
                                @if($compra->estado_compra === 'pagada')
                                    <span style="color: #00e676; font-weight: 600;">{{ ucfirst($compra->estado_compra) }}</span>
                                @elseif($compra->estado_compra === 'pendiente')
                                    <span style="color: #ffc107; font-weight: 600;">{{ ucfirst($compra->estado_compra) }}</span>
                                @else
                                    <span>{{ ucfirst($compra->estado_compra) }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($compra->fecha_compra)->timezone('Europe/Madrid')->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;"
                                        href="{{ route('compra.detail', $compra->id) }}">Ver</a>

                                    @if($compra->factura)
                                        <a class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;"
                                            href="{{ route('usuario.facturacion.detail', $compra->factura->id) }}">Factura</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

    <div class="d-flex gap-2 flex-wrap" style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('usuario.facturacion.index') }}">Ver Facturas</a>
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Explorar Beats</a>
    </div>
@endsection