@extends('layouts.master')

@section('title', 'Carrito')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">Marketplace</p>
            <h1>Carrito</h1>
            <p class="muted">Revisa los productos seleccionados antes de continuar con el pedido.</p>
        </div>
    </div>

    @if($beats->isEmpty() && $colecciones->isEmpty() && $servicios->isEmpty() && ($planes ?? collect())->isEmpty())
        <section class="studio-panel">
            <div class="studio-empty">
                <h2>El carrito está vacío</h2>
                <p class="muted">Explora el catálogo para añadir beats o colecciones.</p>
                <a href="{{ route('beat.index') }}" class="btn btn--primary">Explorar beats</a>
            </div>
        </section>
    @else
        <div class="area-grid">
            <section class="studio-panel">
                @if($beats->count())
                    <div class="area-block">
                        <h2>Beats</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle table-lb studio-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Licencia</th>
                                        <th class="text-end">Base</th>
                                        <th class="text-end">Licencia</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($beats as $linea)
                                    <tr>
                                        <td><strong>{{ $linea['producto']->titulo_beat }}</strong></td>
                                        <td>
                                            <span class="license-chip">{{ $linea['spec']['titulo'] }}</span>
                                            <small class="d-block studio-table__muted">{{ $linea['spec']['formato'] }}</small>
                                        </td>
                                        <td class="text-end">{{ number_format($linea['precio_base'], 2, ',', '.') }} €</td>
                                        <td class="text-end">{{ number_format($linea['precio_licencia'], 2, ',', '.') }} €</td>
                                        <td class="text-end fw-bold">{{ number_format($linea['precio_final'], 2, ',', '.') }} €</td>
                                        <td class="text-end">
                                            <a href="{{ route('carrito.remove', ['type'=>'beat','id'=>$linea['clave']]) }}" class="btn btn--ghost btn-sm">Eliminar</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if($colecciones->count())
                    <div class="area-block">
                        <h2>Colecciones</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle table-lb studio-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th class="text-center">Beats</th>
                                        <th>Licencia</th>
                                        <th class="text-end">Base</th>
                                        <th class="text-end">Licencia</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($colecciones as $linea)
                                    <tr>
                                        <td><strong>{{ $linea['producto']->titulo_coleccion }}</strong></td>
                                        <td class="text-center">{{ $linea['producto']->beats->count() }}</td>
                                        <td>
                                            <span class="license-chip">{{ $linea['spec']['titulo'] }}</span>
                                            <small class="d-block studio-table__muted">{{ $linea['spec']['formato'] }}</small>
                                        </td>
                                        <td class="text-end">{{ number_format($linea['precio_base'], 2, ',', '.') }} €</td>
                                        <td class="text-end">{{ number_format($linea['precio_licencia'], 2, ',', '.') }} €</td>
                                        <td class="text-end fw-bold">{{ number_format($linea['precio_final'], 2, ',', '.') }} €</td>
                                        <td class="text-end">
                                            <a href="{{ route('carrito.remove', ['type'=>'coleccion','id'=>$linea['clave']]) }}" class="btn btn--ghost btn-sm">Eliminar</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if($servicios->count())
                    <div class="area-block">
                        <h2>Servicios</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle table-lb studio-table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Ingeniero</th>
                                        <th>Encargo</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($servicios as $linea)
                                    <tr>
                                        <td><strong>{{ $linea['producto']->titulo_servicio }}</strong></td>
                                        <td>{{ $linea['producto']->usuario->nombre_usuario ?? 'Desconocido' }}</td>
                                        <td class="studio-table__muted">#{{ $linea['proyecto']->id }}</td>
                                        <td class="text-end fw-bold">{{ number_format($linea['precio_final'], 2, ',', '.') }} €</td>
                                        <td class="text-end">
                                            <a href="{{ route('carrito.remove', ['type'=>'servicio','id'=>$linea['clave']]) }}" class="btn btn--ghost btn-sm">Eliminar</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(($planes ?? collect())->count())
                    <div class="area-block">
                        <h2>Planes</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle table-lb studio-table">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Rol</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($planes as $linea)
                                    <tr>
                                        <td><strong>{{ $linea['nombre_producto'] }}</strong></td>
                                        <td>{{ ucfirst($linea['rol']->nombre_rol) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($linea['precio_final'], 2, ',', '.') }} €</td>
                                        <td class="text-end">
                                            <a href="{{ route('carrito.remove', ['type'=>'plan','id'=>$linea['clave']]) }}" class="btn btn--ghost btn-sm">Eliminar</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </section>

            <aside class="area-summary">
                <span>Total</span>
                <strong>{{ number_format($total, 2, ',', '.') }} €</strong>
                <div class="area-summary__actions">
                    <a href="{{ route('compra.checkout.show') }}" class="btn btn--primary w-100">Tramitar pedido</a>
                    <a href="{{ route('carrito.clear') }}" class="btn btn--ghost w-100">Vaciar carrito</a>
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection
