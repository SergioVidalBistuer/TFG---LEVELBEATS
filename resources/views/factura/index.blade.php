@extends('layouts.master')

@section('title', 'Mi Historial de Facturación')

@section('content')

    <h1>Historial de Compras y Facturas</h1>

    @if($facturas->isEmpty())
        <div class="panel" style="text-align: center; padding: 40px;">
            <p style="font-size: 18px; color: rgba(255,255,255,0.7);">No tienes facturas todavía.</p>
            <a href="{{ route('beat.index') }}" class="btn btn--primary" style="margin-top: 20px;">Explorar Beats</a>
        </div>
    @else

        <div style="overflow-x: auto;">
            <table class="table-lb">
                <thead>
                    <tr>
                        <th>Nº Factura</th>
                        <th>Base Imponible</th>
                        <th>Impuestos</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($facturas as $factura)
                    <tr>
                        <td style="font-weight: 600;">{{ $factura->numero_factura }}</td>
                        <td>{{ $factura->base_imponible }} €</td>
                        <td>{{ $factura->importe_impuestos }} €</td>
                        <td style="font-weight: 600; font-size: 16px;">{{ $factura->importe_total }} €</td>
                        <td>{{ $factura->fecha_emision }}</td>
                        <td>
                            @if($factura->pago_confirmado)
                                <span style="color: #00e676; font-weight: 600;">Confirmado</span>
                            @else
                                <span style="color: #ffc107; font-weight: 600;">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;" href="{{ route('usuario.facturacion.detail', $factura->id) }}">Ver detalle</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @endif

    <div style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('beat.index') }}">Volver al Inicio</a>
    </div>
@endsection
