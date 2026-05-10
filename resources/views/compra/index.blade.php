@extends('layouts.master')

@section('title', 'Mis Compras')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">{{ auth()->user()->esAdmin() ? 'Admin' : 'Mi Área' }}</p>
            <h1>{{ auth()->user()->esAdmin() ? 'Pedidos de usuarios' : 'Mis compras' }}</h1>
            <p class="muted">Historial de transacciones realizadas en LevelBeats.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('usuario.facturacion.index') }}">Ver facturas</a>
    </div>

    <section class="studio-panel">
        @if($compras->isEmpty())
            <div class="studio-empty">
                <h2>No tienes compras todavía</h2>
                <p class="muted">Explora el catálogo para adquirir tus primeros productos.</p>
                <a href="{{ route('beat.index') }}" class="btn btn--primary">Explorar beats</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle table-lb studio-table">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            @if(auth()->user()->esAdmin())
                                <th>Comprador</th>
                            @endif
                            <th class="text-end">Total</th>
                            <th>Método</th>
                            <th class="text-center">Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($compras as $compra)
                            <tr>
                                <td><strong>#{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                @if(auth()->user()->esAdmin())
                                    <td>{{ $compra->comprador->nombre_usuario ?? 'Desconocido' }}</td>
                                @endif
                                <td class="text-end fw-bold">{{ number_format($compra->importe_total, 2, ',', '.') }} €</td>
                                <td>{{ ucfirst($compra->metodo_de_pago) }}</td>
                                <td class="text-center"><span class="studio-badge studio-badge--public">{{ ucfirst($compra->estado_compra) }}</span></td>
                                <td class="studio-table__muted">{{ \Carbon\Carbon::parse($compra->fecha_compra)->timezone('Europe/Madrid')->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="studio-actions">
                                        <a href="{{ route('compra.detail', $compra->id) }}">Ver</a>
                                        @if($compra->factura)
                                            <a href="{{ route('usuario.facturacion.detail', $compra->factura->id) }}">Factura</a>
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
