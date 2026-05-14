@extends('layouts.master')

@section('title', auth()->user()->esAdmin() ? 'Admin - Pedidos' : 'Mis Compras')

@section('content')
@if(auth()->user()->esAdmin())
    <div class="admin-page">
        <a href="{{ route('admin.dashboard.index') }}" class="admin-back-link">← Volver al Dashboard</a>

        <header class="admin-page__head">
            <div>
                <span class="admin-kicker">Admin</span>
                <h1>Pedidos</h1>
                <p>Historial de transacciones realizadas en LevelBeats.</p>
            </div>
            <a class="btn btn--ghost" href="{{ route('usuario.facturacion.index') }}">Ver facturas</a>
        </header>

        <section class="admin-table-card">
            @if($compras->isEmpty())
                <div class="admin-empty">No hay pedidos registrados.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Comprador</th>
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
                                    <td><span class="admin-id">#{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                    <td><strong>{{ $compra->comprador->nombre_usuario ?? 'Desconocido' }}</strong></td>
                                    <td class="text-end fw-bold">{{ number_format($compra->importe_total, 2, ',', '.') }} €</td>
                                    <td class="admin-muted">{{ ucfirst($compra->metodo_de_pago) }}</td>
                                    <td class="text-center"><span class="admin-badge admin-badge--accent">{{ ucfirst($compra->estado_compra) }}</span></td>
                                    <td class="admin-muted">{{ \Carbon\Carbon::parse($compra->fecha_compra)->timezone('Europe/Madrid')->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="admin-actions">
                                            <a href="{{ route('compra.detail', $compra->id) }}">Ver</a>
                                            @if($compra->factura)
                                                <a href="{{ route('compra.factura.download', $compra->id) }}" target="_blank" rel="noopener">Factura PDF</a>
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
@else
    <div class="area-page">
        <div class="area-page__head">
            <div>
                <p class="studio-eyebrow">Mi Área</p>
                <h1>Mis compras</h1>
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
                                    <td class="text-end fw-bold">{{ number_format($compra->importe_total, 2, ',', '.') }} €</td>
                                    <td>{{ ucfirst($compra->metodo_de_pago) }}</td>
                                    <td class="text-center"><span class="studio-badge studio-badge--public">{{ ucfirst($compra->estado_compra) }}</span></td>
                                    <td class="studio-table__muted">{{ \Carbon\Carbon::parse($compra->fecha_compra)->timezone('Europe/Madrid')->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <div class="studio-actions">
                                            <a href="{{ route('compra.detail', $compra->id) }}">Ver</a>
                                            @if($compra->factura)
                                                <a href="{{ route('compra.factura.download', $compra->id) }}" target="_blank" rel="noopener">Factura PDF</a>
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
@endif
@endsection
