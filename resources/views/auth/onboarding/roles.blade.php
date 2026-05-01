@extends('layouts.master')

@section('title', 'Bienvenido - Elige tu camino')

@section('content')
<div style="max-width: 800px; margin: 40px auto; text-align: center;">
    
    <h1 style="font-size: 32px; margin-bottom: 8px;">¿Cómo vas a usar LevelBeat?</h1>
    <p style="color: rgba(255,255,255,0.6); margin-bottom: 40px; font-size: 16px;">Selecciona tu vía principal. Podrás modificarlo o ampliarlo más adelante.</p>

    <div class="row g-4" style="text-align: left;">
        
        <!-- CLIENTE -->
        <div class="col-md-4">
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 24px; height: 100%; display: flex; flex-direction: column;">
                <h3 style="margin-top: 0; color: #fff;">Solo Cliente</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 14px; flex-grow: 1;">Quiero comprar instrumentales exclusivas y encargar másters a ingenieros pro.</p>
                
                <form action="{{ route('onboarding.setRole') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="role" value="cliente">
                    <button type="submit" class="btn btn--primary" style="width: 100%;">Soy Cliente</button>
                </form>
            </div>
        </div>

        <!-- PRODUCTOR -->
        <div class="col-md-4">
            <div style="background: rgba(0,230,118,0.05); border: 1px solid rgba(0,230,118,0.2); border-radius: 8px; padding: 24px; height: 100%; display: flex; flex-direction: column;">
                <h3 style="margin-top: 0; color: #00e676;">Productor Musical</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 14px; flex-grow: 1;">Hago bases instrumentales y quiero licenciar mis beats para que otros canten sobre ellos.</p>
                
                <form action="{{ route('onboarding.setRole') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="role" value="productor">
                    <button type="submit" class="btn btn--primary" style="width: 100%; background: #00e676; color: #000; border-color: #00e676;">Vender Beats</button>
                </form>
            </div>
        </div>

        <!-- INGENIERO -->
        <div class="col-md-4">
            <div style="background: rgba(0,212,255,0.05); border: 1px solid rgba(0,212,255,0.2); border-radius: 8px; padding: 24px; height: 100%; display: flex; flex-direction: column;">
                <h3 style="margin-top: 0; color: #00d4ff;">Ingeniero de Sonido</h3>
                <p style="color: rgba(255,255,255,0.5); font-size: 14px; flex-grow: 1;">Ofrezco servicios técnicos B2B como Mezcla, Máster o afinación de voces profesional.</p>
                
                <form action="{{ route('onboarding.setRole') }}" method="POST" style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="role" value="ingeniero">
                    <button type="submit" class="btn btn--primary" style="width: 100%; background: #00d4ff; color: #000; border-color: #00d4ff;">Ofrecer Servicios</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
