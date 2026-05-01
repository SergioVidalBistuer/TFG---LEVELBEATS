@extends('layouts.master')

@section('title', 'Finalizar Pedido')

@section('content')
<div class="container section">
    <div style="margin-bottom: 32px;">
        <a href="{{ route('carrito.index') }}" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 14px;">&larr; Volver al Carrito</a>
        <h1 style="margin-top: 8px;">Finalizar Pedido</h1>
        <p style="color: rgba(255,255,255,0.6); margin: 0;">Revisa tus datos y selecciona tu método de pago seguro.</p>
    </div>
    
    <form action="{{ route('compra.checkout.process') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            
            <!-- DIRECCIÓN -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); padding: 32px; border-radius: 4px;">
                <h2 style="font-size: 18px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px; color: #fff;">Datos de Facturación Opcionales</h2>
                <p style="color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 16px;">Confirma o introduce tu dirección para la factura. Repercutirá en los datos de tu perfil base.</p>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <label style="color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 4px;">Dirección completa (Calle, número):</label>
                        <input type="text" name="calle" value="{{ old('calle', $usuario->calle) }}" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: #fff;">
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label style="color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 4px;">Localidad:</label>
                            <input type="text" name="localidad" value="{{ old('localidad', $usuario->localidad) }}" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: #fff;">
                        </div>
                        <div style="flex: 1;">
                            <label style="color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 4px;">Provincia:</label>
                            <input type="text" name="provincia" value="{{ old('provincia', $usuario->provincia) }}" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: #fff;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label style="color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 4px;">País:</label>
                            <input type="text" name="pais" value="{{ old('pais', $usuario->pais) }}" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: #fff;">
                        </div>
                        <div style="flex: 1;">
                            <label style="color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 4px;">Cód. Postal:</label>
                            <input type="text" name="codigo_postal" value="{{ old('codigo_postal', $usuario->codigo_postal) }}" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: #fff;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAGO -->
            <div style="background: rgba(0,230,118,0.02); border: 1px solid rgba(0,230,118,0.2); padding: 32px; border-radius: 4px;">
                <h2 style="font-size: 18px; margin-bottom: 24px; border-bottom: 1px solid rgba(0,230,118,0.2); padding-bottom: 12px; color: #00e676;">Resumen y Pago</h2>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; background: rgba(0,0,0,0.2); padding: 16px; border-radius: 4px;">
                    <span style="font-size: 16px; color: rgba(255,255,255,0.7);">Importe Total:</span>
                    <span style="font-size: 28px; font-weight: bold; color: #fff;">{{ $total }} €</span>
                </div>

                <label style="display: block; margin-bottom: 16px; color: rgba(255,255,255,0.8); font-weight: 600;">Selecciona un Método de Pago:</label>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 32px;">
                    <label style="background: rgba(255,255,255,0.05); padding: 16px; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; cursor: pointer; display: flex; align-items: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <input type="radio" name="metodo_de_pago" value="paypal" checked style="margin-right: 12px; accent-color: #00e676;">
                        <span style="color: #fff;">PayPal</span>
                    </label>
                    <label style="background: rgba(255,255,255,0.05); padding: 16px; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; cursor: pointer; display: flex; align-items: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <input type="radio" name="metodo_de_pago" value="tarjeta" style="margin-right: 12px; accent-color: #00e676;">
                        <span style="color: #fff;">Tarjeta de Crédito / Débito</span>
                    </label>
                    <label style="background: rgba(255,255,255,0.05); padding: 16px; border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; cursor: pointer; display: flex; align-items: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <input type="radio" name="metodo_de_pago" value="transferencia" style="margin-right: 12px; accent-color: #00e676;">
                        <span style="color: #fff;">Transferencia Bancaria</span>
                    </label>
                </div>

                @error('metodo_de_pago')
                    <div style="color: #ff5252; margin-bottom: 16px; font-size: 13px;">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn--primary" style="width: 100%; font-size: 16px; padding: 16px; background: #00e676; color: #000; font-weight: bold; border: none;">Confirmar y Pagar</button>
            </div>

        </div>
    </form>
</div>
@endsection
