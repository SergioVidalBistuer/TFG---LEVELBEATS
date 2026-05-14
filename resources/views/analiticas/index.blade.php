@extends('layouts.master')

@section('title', 'Analíticas | LevelBeats')

@php
    $money = fn($value) => number_format((float) $value, 2, ',', '.') . ' €';
@endphp

@section('content')
<div class="analytics-page">
    <header class="analytics-hero">
        <div>
            <span class="analytics-role-badge">
                @if($esProductor && $esIngeniero)
                    Studio profesional
                @elseif($esProductor)
                    Productor
                @elseif($esIngeniero)
                    Ingeniero
                @else
                    Mi actividad
                @endif
            </span>
            <h1>Analíticas</h1>
            <p>{{ $subtitulo }}</p>
        </div>
    </header>

    <section class="analytics-section">
        <div class="analytics-section__head">
            <div>
                <h2>Resumen general</h2>
                <p>Actividad como comprador y usuario dentro de LevelBeats.</p>
            </div>
        </div>

        <div class="analytics-grid">
            <article class="analytics-card">
                <span class="analytics-card__label">Compras realizadas</span>
                <strong class="analytics-card__value">{{ $generales['compras_realizadas'] }}</strong>
            </article>
            <article class="analytics-card">
                <span class="analytics-card__label">Total gastado</span>
                <strong class="analytics-card__value">{{ $money($generales['total_gastado']) }}</strong>
            </article>
            <article class="analytics-card">
                <span class="analytics-card__label">Beats adquiridos</span>
                <strong class="analytics-card__value">{{ $generales['beats_adquiridos'] }}</strong>
            </article>
            <article class="analytics-card">
                <span class="analytics-card__label">Colecciones adquiridas</span>
                <strong class="analytics-card__value">{{ $generales['colecciones_adquiridas'] }}</strong>
            </article>
            <article class="analytics-card">
                <span class="analytics-card__label">Servicios contratados</span>
                <strong class="analytics-card__value">{{ $generales['servicios_contratados'] }}</strong>
            </article>
            <article class="analytics-card">
                <span class="analytics-card__label">Conversaciones</span>
                <strong class="analytics-card__value">{{ $generales['conversaciones'] }}</strong>
            </article>
        </div>

        <div class="analytics-actions">
            <a class="btn btn--ghost" href="{{ route('compra.index') }}">Ver compras</a>
            <a class="btn btn--ghost" href="{{ route('usuario.guardados.index') }}">Ver guardados</a>
            <a class="btn btn--ghost" href="{{ route('mensajes.index') }}">Ver mensajes</a>
            <a class="btn btn--primary" href="{{ route('beat.index') }}">Explorar marketplace</a>
        </div>
    </section>

    @if($esProductor && $productor)
        <section class="analytics-section">
            <div class="analytics-section__head">
                <div>
                    <h2>Productor</h2>
                    <p>Rendimiento de beats, colecciones y ventas musicales.</p>
                </div>
            </div>

            <div class="analytics-grid">
                <article class="analytics-card"><span class="analytics-card__label">Beats publicados</span><strong class="analytics-card__value">{{ $productor['beats_publicados'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Colecciones publicadas</span><strong class="analytics-card__value">{{ $productor['colecciones_publicadas'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Ventas de beats</span><strong class="analytics-card__value">{{ $productor['ventas_beats'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Ventas de colecciones</span><strong class="analytics-card__value">{{ $productor['ventas_colecciones'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Ingresos</span><strong class="analytics-card__value">{{ $money($productor['ingresos']) }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Ticket medio</span><strong class="analytics-card__value">{{ $money($productor['ticket_medio']) }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Beats ocultos</span><strong class="analytics-card__value">{{ $productor['beats_ocultos'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Colecciones ocultas</span><strong class="analytics-card__value">{{ $productor['colecciones_ocultas'] }}</strong></article>
            </div>

            <div class="analytics-split">
                @include('analiticas.partials.sales-table', ['items' => $productor['ultimas_ventas'], 'title' => 'Últimas ventas'])
                @include('analiticas.partials.top-products', ['items' => $productor['productos_mas_vendidos'], 'title' => 'Productos más vendidos'])
            </div>

            <div class="analytics-actions">
                <a class="btn btn--ghost" href="{{ route('studio.beats.index') }}">Ver mis beats</a>
                <a class="btn btn--ghost" href="{{ route('studio.colecciones.index') }}">Ver mis colecciones</a>
                <a class="btn btn--primary" href="{{ route('studio.beats.create') }}">Crear beat</a>
                <a class="btn btn--primary" href="{{ route('studio.colecciones.create') }}">Crear colección</a>
            </div>
        </section>
    @endif

    @if($esIngeniero && $ingeniero)
        <section class="analytics-section">
            <div class="analytics-section__head">
                <div>
                    <h2>Ingeniero</h2>
                    <p>Rendimiento de servicios, encargos y proyectos contratados.</p>
                </div>
            </div>

            <div class="analytics-grid">
                <article class="analytics-card"><span class="analytics-card__label">Servicios publicados</span><strong class="analytics-card__value">{{ $ingeniero['servicios_publicados'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Servicios activos</span><strong class="analytics-card__value">{{ $ingeniero['servicios_activos'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Servicios inactivos</span><strong class="analytics-card__value">{{ $ingeniero['servicios_inactivos'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Proyectos recibidos</span><strong class="analytics-card__value">{{ $ingeniero['proyectos_recibidos'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Proyectos abiertos</span><strong class="analytics-card__value">{{ $ingeniero['proyectos_abiertos'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Proyectos cerrados</span><strong class="analytics-card__value">{{ $ingeniero['proyectos_cerrados'] }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Ingresos por servicios</span><strong class="analytics-card__value">{{ $money($ingeniero['ingresos']) }}</strong></article>
                <article class="analytics-card"><span class="analytics-card__label">Plazo medio</span><strong class="analytics-card__value">{{ $ingeniero['plazo_medio'] ? round($ingeniero['plazo_medio'], 1) . ' días' : '-' }}</strong></article>
            </div>

            <div class="analytics-split">
                <article class="analytics-panel">
                    <h3>Últimos proyectos</h3>
                    @if($ingeniero['ultimos_proyectos']->isEmpty())
                        <div class="analytics-empty">Todavía no hay proyectos recibidos.</div>
                    @else
                        <table class="analytics-table">
                            <tbody>
                                @foreach($ingeniero['ultimos_proyectos'] as $proyecto)
                                    <tr>
                                        <td><strong>{{ $proyecto->titulo_proyecto }}</strong><small>{{ $proyecto->cliente->nombre_usuario ?? 'Cliente' }}</small></td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $proyecto->estado_proyecto)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </article>
                @include('analiticas.partials.top-services', ['items' => $ingeniero['servicios_mas_contratados'], 'title' => 'Servicios más contratados'])
            </div>

            <div class="analytics-actions analytics-actions--engineer">
                <a class="btn btn--ghost" href="{{ route('studio.servicios.index') }}">Ver servicios</a>
                <a class="btn btn--ghost" href="{{ route('studio.proyectos.index') }}">Ver proyectos</a>
                <a class="btn btn--ghost" href="{{ route('mensajes.index') }}">Ver mensajes</a>
                <a class="btn btn--primary" href="{{ route('studio.servicios.create') }}">Crear servicio</a>
            </div>
        </section>
    @endif
</div>
@endsection
