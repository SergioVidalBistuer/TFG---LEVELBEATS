@php
    $comprador = $compra->comprador;
    $fechaEmision = optional($factura->fecha_emision)->format('d/m/Y') ?? 'No disponible';
    $fechaCompra = optional($compra->fecha_compra)->format('d/m/Y H:i') ?? 'No disponible';
    $estadoPago = $factura->pago_confirmado ? 'Pago confirmado' : 'Pago pendiente';
    $direccion = collect([
        $comprador->calle ?? null,
        trim(($comprador->codigo_postal ?? '') . ' ' . ($comprador->localidad ?? '')) ?: null,
        $comprador->provincia ?? null,
        $comprador->pais ?? null,
    ])->filter()->implode(', ');

    $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' €';
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        @page {
            margin: 42px 48px 54px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #172033;
            font-size: 11px;
            line-height: 1.45;
            background: #ffffff;
        }

        .pdf-footer {
            position: fixed;
            bottom: -32px;
            left: 0;
            right: 0;
            border-top: 1px solid #d7dce7;
            padding-top: 8px;
            color: #6f7686;
            font-size: 9px;
        }

        .page-number:after {
            content: "Página " counter(page);
        }

        .topbar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 26px;
        }

        .topbar td {
            vertical-align: top;
        }

        .brand-logo {
            width: 112px;
            height: auto;
            margin-bottom: 5px;
        }

        .brand-fallback {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: .08em;
            color: #111827;
        }

        .brand-meta {
            color: #6f7686;
            font-size: 10px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0 0 8px;
            font-size: 30px;
            color: #111827;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .invoice-number {
            display: inline-block;
            border: 1px solid #d7dce7;
            border-radius: 999px;
            padding: 5px 11px;
            color: #4b5563;
            font-size: 10px;
        }

        .status {
            display: inline-block;
            margin-top: 8px;
            border: 1px solid #a855f7;
            color: #5b21b6;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .grid td {
            width: 50%;
            vertical-align: top;
            padding-right: 12px;
        }

        .box {
            border: 1px solid #d7dce7;
            border-radius: 12px;
            padding: 14px;
            background: #fbfbfd;
        }

        .box h2 {
            margin: 0 0 10px;
            font-size: 11px;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .box p {
            margin: 3px 0;
            color: #3b4354;
        }

        .muted {
            color: #6f7686;
        }

        .data-table,
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table {
            margin-top: 12px;
        }

        .data-table th {
            background: #111827;
            color: #f9fafb;
            padding: 9px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .data-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 9px 8px;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 260px;
            margin: 20px 0 22px auto;
            border: 1px solid #d7dce7;
            border-radius: 12px;
            padding: 12px;
        }

        .total-table td {
            padding: 6px 0;
        }

        .total-table .label {
            color: #6f7686;
        }

        .total-table .grand td {
            border-top: 1px solid #d7dce7;
            padding-top: 10px;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .note {
            border-left: 3px solid #a855f7;
            padding: 10px 12px;
            background: #faf7ff;
            color: #4b5563;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="pdf-footer">
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td>LevelBeats · {{ $factura->numero_factura }}</td>
                <td style="text-align:right;"><span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    <table class="topbar">
        <tr>
            <td>
                @if($logoDataUri)
                    <img class="brand-logo" src="{{ $logoDataUri }}" alt="LevelBeats">
                @else
                    <div class="brand-fallback">LEVELBEATS</div>
                @endif
                <div class="brand-meta">
                    LevelBeats<br>
                    contacto@level-beats.com
                </div>
            </td>
            <td class="invoice-title">
                <h1>Factura</h1>
                <div class="invoice-number">{{ $factura->numero_factura }}</div><br>
                <div class="status">{{ $estadoPago }}</div>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td>
                <div class="box">
                    <h2>Emitida por</h2>
                    <p><strong>LevelBeats</strong></p>
                    <p>Plataforma digital de beats, colecciones y servicios creativos.</p>
                    <p class="muted">contacto@level-beats.com</p>
                </div>
            </td>
            <td>
                <div class="box">
                    <h2>Comprador</h2>
                    <p><strong>{{ $comprador->nombre_usuario ?? 'No disponible' }}</strong></p>
                    <p>{{ $comprador->direccion_correo ?? 'No disponible' }}</p>
                    @if($direccion)
                        <p class="muted">{{ $direccion }}</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td>
                <div class="box">
                    <h2>Datos de factura</h2>
                    <p><strong>Fecha de emisión:</strong> {{ $fechaEmision }}</p>
                    <p><strong>Estado:</strong> {{ $estadoPago }}</p>
                </div>
            </td>
            <td>
                <div class="box">
                    <h2>Datos de compra</h2>
                    <p><strong>Compra:</strong> #{{ str_pad((string) $compra->id, 4, '0', STR_PAD_LEFT) }}</p>
                    <p><strong>Método:</strong> {{ ucfirst($compra->metodo_de_pago ?? 'No registrado') }}</p>
                    <p><strong>Fecha:</strong> {{ $fechaCompra }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($compra->estado_compra ?? 'No registrado') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Producto / servicio</th>
                <th>Tipo</th>
                <th>Licencia</th>
                <th>Formato</th>
                <th class="text-right">Base</th>
                <th class="text-right">Licencia</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lineas as $linea)
                <tr>
                    <td><strong>{{ $linea['producto'] }}</strong></td>
                    <td>{{ $linea['tipo'] }}</td>
                    <td>{{ $linea['licencia'] }}</td>
                    <td>{{ $linea['formato'] }}</td>
                    <td class="text-right">{{ $money($linea['precio_base']) }}</td>
                    <td class="text-right">{{ $money($linea['precio_licencia']) }}</td>
                    <td class="text-right"><strong>{{ $money($linea['total']) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay líneas de compra registradas para esta factura.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <table class="total-table">
            <tr>
                <td class="label">Base imponible</td>
                <td class="text-right">{{ $money($factura->base_imponible) }}</td>
            </tr>
            <tr>
                <td class="label">Impuestos</td>
                <td class="text-right">{{ $money($factura->importe_impuestos) }}</td>
            </tr>
            <tr class="grand">
                <td>Total EUR</td>
                <td class="text-right">{{ $money($factura->importe_total) }}</td>
            </tr>
        </table>
    </div>

    <div class="note">
        Esta factura se emite automáticamente por LevelBeats para la compra indicada. Conserva este documento como justificante de la transacción. Gracias por usar LevelBeats.
    </div>
</body>
</html>
