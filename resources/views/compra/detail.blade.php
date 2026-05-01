@extends('layouts.master')

@section('title', 'Detalle Compra #' . $compra->id)

@section('content')

    <h1>Compra #{{ $compra->id }}</h1>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="panel panel--dark" style="padding: 24px;">
                <h3 style="margin-top: 0;">Información de la compra</h3>
                <table style="width:100%;">
                    <tr>
                        <td style="padding: 8px 0; color: rgba(255,255,255,.6);">Estado</td>
                        <td style="padding: 8px 0; font-weight: 600;">
                            @if($compra->estado_compra === 'pagada')
                                <span style="color: #00e676;">{{ ucfirst($compra->estado_compra) }}</span>
                            @elseif($compra->estado_compra === 'pendiente')
                                <span style="color: #ffc107;">{{ ucfirst($compra->estado_compra) }}</span>
                            @else
                                {{ ucfirst($compra->estado_compra) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: rgba(255,255,255,.6);">Método de pago</td>
                        <td style="padding: 8px 0;">{{ ucfirst($compra->metodo_de_pago) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: rgba(255,255,255,.6);">Fecha</td>
                        <td style="padding: 8px 0;">{{ $compra->fecha_compra }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: rgba(255,255,255,.6);">Total</td>
                        <td style="padding: 8px 0; font-size: 22px; font-weight: 700; color: #fff;">{{ $compra->importe_total }} €</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            @if($compra->factura)
                <div class="cart-summary" style="text-align: left; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">Factura</h3>
                    <p>Nº Factura: <strong>{{ $compra->factura->numero_factura }}</strong></p>
                    <p>Base imponible: {{ $compra->factura->base_imponible }} €</p>
                    <p>Impuestos: {{ $compra->factura->importe_impuestos }} €</p>
                    <p style="font-size: 20px; font-weight: 700;">Total: {{ $compra->factura->importe_total }} €</p>
                    <a class="btn btn--primary" href="{{ route('usuario.facturacion.detail', $compra->factura->id) }}" style="margin-top: 12px;">Ver factura completa</a>
                </div>
            @endif

            @if($compra->contrato)
                <div class="cart-summary" style="text-align: left;">
                    <h3 style="margin-top: 0;">Contrato Legal</h3>
                    <p>Tipo: <strong>{{ ucfirst($compra->contrato->tipo_contrato) }}</strong></p>
                    <p>Estado: 
                        @if($compra->contrato->contrato_firmado)
                            <span style="color: #00e676;">Firmado ({{ \Carbon\Carbon::parse($compra->contrato->fecha_firma)->format('d/m/Y') }})</span>
                        @else
                            <span style="color: #ffc107;">Pendiente de Firma</span>
                        @endif
                    </p>
                    @if($compra->contrato->url_contrato_pdf)
                        <a class="btn btn--ghost" href="{{ asset($compra->contrato->url_contrato_pdf) }}" target="_blank" style="margin-top: 12px; color: #00e676; border-color: rgba(0,230,118,0.3);">📄 Descargar PDF</a>
                    @endif
                </div>
            @elseif($compra->url_contrato_pdf)
                <div class="cart-summary" style="text-align: left;">
                    <h3 style="margin-top: 0;">Contrato Legal</h3>
                    <p>Estado: Activo</p>
                    <a class="btn btn--ghost" href="{{ asset($compra->url_contrato_pdf) }}" target="_blank" style="margin-top: 12px; color: #00e676; border-color: rgba(0,230,118,0.3);">📄 Descargar Documento Público</a>
                </div>
            @endif
        </div>
    </div>

    {{-- BEATS DE ESTA COMPRA --}}
    @if($compra->beats->count())
        <h2 style="margin-top: 32px;">Beats comprados</h2>
        <div style="overflow-x: auto;">
            <table class="table-lb">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Género</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($compra->beats as $beat)
                    <tr>
                        <td>{{ $beat->titulo_beat }}</td>
                        <td>{{ $beat->genero_musical ?? '-' }}</td>
                        <td style="font-weight: 600;">{{ $beat->precio_base_licencia }} €</td>
                        <td>
                            <a class="btn btn--ghost" style="font-size: 13px; padding: 6px 14px;" href="{{ route('beat.detail', $beat->id) }}">Ver Beat</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif



    <div style="margin-top: 30px;">
        <a class="btn btn--ghost" href="{{ route('compra.index') }}">Volver a Mis Compras</a>
    </div>
@endsection
