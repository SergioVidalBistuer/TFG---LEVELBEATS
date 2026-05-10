@extends('layouts.master')

@section('title', 'Finalizar pedido')

@section('content')
<div class="area-page">
    <div class="area-page__head">
        <div>
            <p class="studio-eyebrow">Checkout</p>
            <h1>Finalizar pedido</h1>
            <p class="muted">Revisa tus datos y selecciona el método de pago.</p>
        </div>
        <a class="btn btn--ghost" href="{{ route('carrito.index') }}">Volver al carrito</a>
    </div>

    <form class="studio-form" action="{{ route('compra.checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <section class="studio-form-panel h-100">
                    <h2 class="area-panel-title">Datos de facturación</h2>
                    <p class="muted">Estos datos son opcionales y se guardarán en tu perfil para futuras compras.</p>

                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <div class="studio-field">
                                <label for="calle">Dirección</label>
                                <input id="calle" class="form-control form-lb__input" type="text" name="calle" value="{{ old('calle', $usuario->calle) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="studio-field">
                                <label for="localidad">Localidad</label>
                                <input id="localidad" class="form-control form-lb__input" type="text" name="localidad" value="{{ old('localidad', $usuario->localidad) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="studio-field">
                                <label for="provincia">Provincia</label>
                                <input id="provincia" class="form-control form-lb__input" type="text" name="provincia" value="{{ old('provincia', $usuario->provincia) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="studio-field">
                                <label for="pais">País</label>
                                <input id="pais" class="form-control form-lb__input" type="text" name="pais" value="{{ old('pais', $usuario->pais) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="studio-field">
                                <label for="codigo_postal">Código postal</label>
                                <input id="codigo_postal" class="form-control form-lb__input" type="text" name="codigo_postal" value="{{ old('codigo_postal', $usuario->codigo_postal) }}">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-5">
                <section class="area-summary area-summary--checkout">
                    <span>Importe total</span>
                    <strong>{{ number_format($total, 2, ',', '.') }} €</strong>

                    <div class="checkout-lines">
                        @foreach(($items['beats'] ?? collect())->merge($items['colecciones'] ?? collect()) as $linea)
                            <div class="checkout-line">
                                <div>
                                    <strong>{{ $linea['nombre_producto'] }}</strong>
                                    <span>{{ ucfirst($linea['tipo']) }} · {{ $linea['spec']['titulo'] }} · {{ $linea['spec']['formato'] }}</span>
                                </div>
                                <em>{{ number_format($linea['precio_final'], 2, ',', '.') }} €</em>
                            </div>
                        @endforeach
                        @foreach(($items['servicios'] ?? collect()) as $linea)
                            <div class="checkout-line">
                                <div>
                                    <strong>{{ $linea['nombre_producto'] }}</strong>
                                    <span>Servicio · Ingeniero: {{ $linea['producto']->usuario->nombre_usuario ?? 'Desconocido' }} · Encargo #{{ $linea['proyecto']->id }}</span>
                                </div>
                                <em>{{ number_format($linea['precio_final'], 2, ',', '.') }} €</em>
                            </div>
                        @endforeach
                    </div>

                    <div class="area-payment-options">
                        <label class="studio-switch studio-switch--compact">
                            <input type="radio" name="metodo_de_pago" value="paypal" checked>
                            <span><strong>PayPal</strong><small>Pago digital externo</small></span>
                        </label>
                        <label class="studio-switch studio-switch--compact">
                            <input type="radio" name="metodo_de_pago" value="tarjeta">
                            <span><strong>Tarjeta</strong><small>Crédito o débito</small></span>
                        </label>
                        <label class="studio-switch studio-switch--compact">
                            <input type="radio" name="metodo_de_pago" value="transferencia">
                            <span><strong>Transferencia</strong><small>Pago bancario manual</small></span>
                        </label>
                    </div>

                    @error('metodo_de_pago')
                        <div class="account-feedback account-feedback--error">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn--primary w-100">Confirmar y pagar</button>
                </section>
            </div>
        </div>
    </form>
</div>
@endsection
