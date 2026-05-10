@extends('layouts.master')

@section('title', 'Factura ' . $factura->numero_factura)

@section('content')

    <div style="max-width: 700px; margin: 0 auto;">

        {{-- CABECERA DE LA FACTURA --}}
        <div class="panel panel--dark" style="padding: 32px; margin-bottom: 24px;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 style="margin: 0 0 4px; font-size: 28px;">Factura</h1>
                    <p style="color: rgba(255,255,255,.5); font-size: 14px; margin: 0;">{{ $factura->numero_factura }}</p>
                </div>
                <div style="text-align: right;">
                    <img src="{{ asset('media/img/LB-09.png') }}" alt="LevelBeats" style="width: 120px; height: auto; object-fit: contain;">
                    <p style="color: rgba(255,255,255,.5); font-size: 12px; margin-top: 6px;">LevelBeats</p>
                </div>
            </div>
        </div>

        {{-- DATOS PRINCIPALES --}}
        <div class="panel" style="padding: 24px; margin-bottom: 24px;">
            <div class="row">
                <div class="col-6">
                    <p style="color:rgba(255,255,255,.5); font-size:12px; text-transform:uppercase; margin-bottom:4px;">Cliente</p>
                    <p style="font-weight:600; margin: 0;">{{ $factura->compra->comprador->nombre_usuario ?? '-' }}</p>
                    <p style="font-size:14px; color: rgba(255,255,255,.6);">{{ $factura->compra->comprador->direccion_correo ?? '' }}</p>
                </div>
                <div class="col-6" style="text-align: right;">
                    <p style="color:rgba(255,255,255,.5); font-size:12px; text-transform:uppercase; margin-bottom:4px;">Fecha emisión</p>
                    <p style="font-weight:600; margin: 0;">{{ $factura->fecha_emision }}</p>
                    <p style="font-size:14px; margin-top: 8px;">
                        @if($factura->pago_confirmado)
                            <span style="color: #00e676; font-weight: 600;">✓ Pago confirmado</span>
                        @else
                            <span style="color: #ffc107; font-weight: 600;">⏳ Pendiente</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- LÍNEAS DE LA FACTURA --}}
        @if($factura->compra->detalles->count())
            <h3 style="margin-bottom: 8px;">Productos y licencias</h3>
            <div style="overflow-x: auto;">
                <table class="table table-borderless align-middle table-lb">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Licencia</th>
                            <th>Formato</th>
                            <th style="text-align:right;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($factura->compra->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->nombre_producto_snapshot }}</td>
                            <td>{{ $detalle->nombre_licencia_snapshot ?? 'Licencia no registrada' }}</td>
                            <td>{{ $detalle->formato_incluido_snapshot ?? '-' }}</td>
                            <td style="text-align:right; font-weight:600;">{{ number_format($detalle->precio_final, 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
        @if($factura->compra->beats->count())
            <h3 style="margin-bottom: 8px;">Beats</h3>
            <div style="overflow-x: auto;">
                <table class="table table-borderless align-middle table-lb">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th style="text-align:right;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($factura->compra->beats as $beat)
                        <tr>
                            <td>{{ $beat->titulo_beat }}</td>
                            <td style="text-align:right; font-weight:600;">{{ $beat->precio_base_licencia }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($factura->compra->colecciones->count())
            <h3 style="margin-top: 24px; margin-bottom: 8px;">Colecciones</h3>
            <div style="overflow-x: auto;">
                <table class="table table-borderless align-middle table-lb">
                    <thead>
                        <tr>
                            <th>Colección</th>
                            <th>Beats incluidos</th>
                            <th style="text-align:right;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($factura->compra->colecciones as $coleccion)
                        <tr>
                            <td>{{ $coleccion->titulo_coleccion }}</td>
                            <td>{{ $coleccion->beats->count() }}</td>
                            <td style="text-align:right; font-weight:600;">{{ $coleccion->precio }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($factura->compra->servicios->count())
            <h3 style="margin-top: 24px; margin-bottom: 8px;">Servicios</h3>
            <div style="overflow-x: auto;">
                <table class="table table-borderless align-middle table-lb">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th style="text-align:right;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($factura->compra->servicios as $servicio)
                        <tr>
                            <td>{{ $servicio->titulo_servicio }}</td>
                            <td style="text-align:right; font-weight:600;">{{ $servicio->precio_servicio }} €</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @endif

        {{-- TOTALES --}}
        <div class="cart-summary" style="margin-top: 24px;">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 6px 0; color: rgba(255,255,255,.6);">Base imponible</td>
                    <td style="padding: 6px 0; text-align: right;">{{ $factura->base_imponible }} €</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: rgba(255,255,255,.6);">Impuestos (IVA)</td>
                    <td style="padding: 6px 0; text-align: right;">{{ $factura->importe_impuestos }} €</td>
                </tr>
                <tr style="border-top: 1px solid var(--line);">
                    <td style="padding: 12px 0; font-weight: 700; font-size: 20px;">Total</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700; font-size: 20px; color: #fff;">{{ $factura->importe_total }} €</td>
                </tr>
            </table>
        </div>

        <div class="d-flex gap-2 flex-wrap" style="margin-top: 30px;">
            <a class="btn btn--ghost" href="{{ route('usuario.facturacion.index') }}">Volver a Facturas</a>
            <a class="btn btn--ghost" href="{{ route('compra.detail', $factura->compra->id) }}">Ver Compra</a>
        </div>

    </div>
@endsection
