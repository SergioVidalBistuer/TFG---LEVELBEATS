@extends('layouts.master')

@section('title', 'Historial de facturación')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">Mi Área</p>
            <h1>Facturación</h1>
            <p class="muted">Historial de facturas asociadas a tus compras.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('compra.index') }}">Ver compras</a>
    </div>

    <section class="studio-panel">
        @if($facturas->isEmpty())
            <div class="studio-empty">
                <h2>No tienes facturas todavía</h2>
                <p class="muted">Cuando completes una compra, su factura aparecerá aquí.</p>
                <a href="{{ route('beat.index') }}" class="btn btn--primary">Explorar beats</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Nº Factura</th>
                            <th class="text-end">Base</th>
                            <th class="text-end">Impuestos</th>
                            <th class="text-end">Total</th>
                            <th>Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($facturas as $factura)
                        <tr>
                            <td><strong>{{ $factura->numero_factura }}</strong></td>
                            <td class="text-end">{{ number_format($factura->base_imponible, 2, ',', '.') }} €</td>
                            <td class="text-end">{{ number_format($factura->importe_impuestos, 2, ',', '.') }} €</td>
                            <td class="text-end fw-bold">{{ number_format($factura->importe_total, 2, ',', '.') }} €</td>
                            <td class="studio-table__muted">{{ $factura->fecha_emision }}</td>
                            <td class="text-center">
                                <span class="studio-badge {{ $factura->pago_confirmado ? 'studio-badge--public' : '' }}">{{ $factura->pago_confirmado ? 'Confirmado' : 'Pendiente' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="studio-actions justify-content-end">
                                    <a href="{{ route('usuario.facturacion.detail', $factura->id) }}">Ver detalle</a>
                                    @if($factura->compra)
                                        <a href="{{ route('compra.factura.download', $factura->compra->id) }}" target="_blank" rel="noopener">Factura PDF</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
