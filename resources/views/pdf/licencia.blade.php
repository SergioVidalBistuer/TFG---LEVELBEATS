@php
    $isColeccion = $detalle->tipo_producto === 'coleccion';
    $productoTipo = $isColeccion ? 'Colección' : 'Beat';
    $compradorNombre = $comprador->nombre_usuario ?? 'No disponible';
    $compradorEmail = $comprador->direccion_correo ?? 'No disponible';
    $licencianteNombre = $licenciante->nombre_usuario ?? 'Productor no disponible';
    $productoNombre = $detalle->nombre_producto_snapshot;
    $licenciaNombre = $detalle->nombre_licencia_snapshot ?? $spec['nombre'];
    $formato = $detalle->formato_incluido_snapshot ?? $spec['formato'];
    $precio = number_format((float) $detalle->precio_final, 2, ',', '.') . ' €';
    $fecha = optional($detalle->fecha)->format('d/m/Y') ?? optional($detalle->compra->fecha_compra)->format('d/m/Y') ?? 'No disponible';
    $factura = $detalle->compra->factura?->numero_factura;
    $numeroCompra = 'Compra #' . $detalle->id_compra . ($factura ? ' · Factura ' . $factura : '');

    $config = [
        'basica' => [
            'titulo' => 'CONTRATO DE LICENCIA BÁSICA NO EXCLUSIVA DE USO DE BEAT',
            'subtitulo' => 'Licencia no exclusiva para uso musical limitado en formato MP3',
            'naturaleza' => 'no exclusiva, limitada, revocable en caso de incumplimiento e intransferible',
            'formatos' => 'MP3',
            'reproducciones' => '50.000 reproducciones en plataformas digitales',
            'copias' => '3.000 copias o descargas distribuidas',
            'videoclips' => '1 videoclip oficial',
            'actuaciones' => '5 actuaciones públicas',
            'monetizacion' => 'monetización permitida dentro de los límites establecidos',
            'exclusividad' => 'El licenciante conserva la titularidad completa de la obra y puede conceder nuevas licencias a terceros.',
        ],
        'premium' => [
            'titulo' => 'CONTRATO DE LICENCIA PREMIUM NO EXCLUSIVA DE USO DE BEAT',
            'subtitulo' => 'Licencia no exclusiva ampliada para explotación musical profesional',
            'naturaleza' => 'no exclusiva, ampliada, revocable en caso de incumplimiento e intransferible',
            'formatos' => 'MP3 + WAV',
            'reproducciones' => '250.000 reproducciones en plataformas digitales',
            'copias' => '10.000 copias o descargas distribuidas',
            'videoclips' => '2 videoclips oficiales',
            'actuaciones' => '25 actuaciones públicas',
            'monetizacion' => 'monetización permitida en plataformas digitales y redes sociales dentro de los límites establecidos',
            'exclusividad' => 'El licenciante conserva la titularidad completa de la obra y puede conceder nuevas licencias a terceros.',
        ],
        'exclusiva' => [
            'titulo' => 'CONTRATO DE LICENCIA EXCLUSIVA DE USO DE BEAT',
            'subtitulo' => 'Licencia exclusiva para explotación musical amplia',
            'naturaleza' => 'exclusiva, amplia, revocable en caso de incumplimiento e intransferible salvo autorización expresa',
            'formatos' => 'MP3 + WAV + STEMS',
            'reproducciones' => 'reproducción ilimitada',
            'copias' => 'copias y descargas ilimitadas',
            'videoclips' => 'videoclips sin límite específico de explotación',
            'actuaciones' => 'actuaciones públicas ilimitadas',
            'monetizacion' => 'monetización permitida de forma amplia conforme a este contrato',
            'exclusividad' => 'Desde la compra, el licenciante no podrá conceder nuevas licencias exclusivas sobre el mismo producto. Las licencias no exclusivas anteriores, si existieran, mantienen su validez.',
        ],
    ][$tipoLicencia] ?? [
        'titulo' => 'CONTRATO DE LICENCIA MUSICAL',
        'subtitulo' => 'Licencia musical registrada en LevelBeats',
        'naturaleza' => 'licencia registrada según las condiciones guardadas en la compra',
        'formatos' => $formato,
        'reproducciones' => $detalle->derechos_snapshot ?? 'No registrado',
        'copias' => 'No registrado',
        'videoclips' => 'No registrado',
        'actuaciones' => 'No registrado',
        'monetizacion' => 'Según condiciones registradas',
        'exclusividad' => 'Según condiciones registradas',
    ];
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $licenciaNombre }} - {{ $productoNombre }}</title>
    <style>
        @page {
            margin: 86px 64px 72px 64px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.52;
        }

        .pdf-header {
            position: fixed;
            top: -62px;
            left: 0;
            right: 0;
            height: 28px;
            border-bottom: 1px solid #d1d5db;
        }

        .pdf-footer {
            position: fixed;
            bottom: -48px;
            left: 0;
            right: 0;
            height: 32px;
            border-top: 1px solid #d1d5db;
            color: #6b7280;
            font-size: 9px;
        }

        .page-number:after {
            content: "Página " counter(page);
        }

        .footer-table,
        .data-table,
        .limits-table,
        .beats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }

        .cover {
            text-align: center;
        }

        .cover-logo {
            width: 250px;
            height: auto;
            margin: 30px auto 38px;
        }

        .brand-fallback {
            display: block;
            margin: 36px 0 8px;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: .04em;
            color: #111827;
        }

        .header-title {
            text-align: left;
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding-top: 2px;
        }

        h1 {
            margin: 0 0 8px;
            color: #111827;
            font-size: 22px;
            line-height: 1.22;
            text-align: center;
            text-transform: uppercase;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #4b5563;
            text-align: center;
            font-size: 12px;
        }

        h2 {
            margin: 20px 0 8px;
            color: #111827;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        h3 {
            margin: 14px 0 6px;
            color: #111827;
            font-size: 12px;
        }

        p {
            margin: 0 0 8px;
        }

        .intro-box {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 14px;
            margin-bottom: 18px;
        }

        .data-table td,
        .limits-table td,
        .beats-table th,
        .beats-table td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            vertical-align: top;
        }

        .data-table td:first-child,
        .limits-table td:first-child {
            width: 34%;
            background: #f3f4f6;
            color: #111827;
            font-weight: 700;
        }

        .beats-table th {
            background: #f3f4f6;
            color: #111827;
            text-align: left;
            font-weight: 700;
        }

        .clause {
            margin-bottom: 12px;
        }

        .page-break {
            page-break-before: always;
        }

        .acceptance {
            border: 1px solid #111827;
            padding: 12px;
            margin-top: 14px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 26px;
        }

        .signature-table td {
            width: 50%;
            border: 0;
            padding: 0 18px 0 0;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 8px;
            margin-top: 34px;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <div class="header-title">LevelBeat - {{ $licenciaNombre }}</div>
    </div>

    <div class="pdf-footer">
        <table class="footer-table">
            <tr>
                <td>LevelBeats · Documento contractual generado electrónicamente</td>
                <td style="text-align:right;"><span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    <div class="cover">
        @if($logoDataUri)
            <img class="cover-logo" src="{{ $logoDataUri }}" alt="LevelBeats">
        @else
            <span class="brand-fallback">LevelBeats</span>
        @endif
        <h1>{{ $config['titulo'] }}</h1>
        <p class="subtitle">{{ $config['subtitulo'] }}</p>
    </div>

    <div class="intro-box">
        <p>
            Este documento regula las condiciones bajo las que <strong>{{ $licencianteNombre }}</strong>,
            en adelante el <strong>Licenciante</strong>, concede a <strong>{{ $compradorNombre }}</strong>,
            en adelante el <strong>Licenciatario</strong>, una licencia de uso sobre el producto musical
            <strong>{{ $productoNombre }}</strong>, adquirido a través de la plataforma LevelBeats.
        </p>
        <p>
            La licencia queda vinculada a la compra identificada como <strong>{{ $numeroCompra }}</strong>
            y a la aceptación electrónica realizada durante el proceso de pago.
        </p>
    </div>

    <h2>Datos particulares de la licencia</h2>
    <table class="data-table">
        <tr><td>Comprador / Licenciatario</td><td>{{ $compradorNombre }}</td></tr>
        <tr><td>Email del comprador</td><td>{{ $compradorEmail }}</td></tr>
        <tr><td>Productor / Licenciante</td><td>{{ $licencianteNombre }}</td></tr>
        <tr><td>Producto licenciado</td><td>{{ $productoNombre }}</td></tr>
        <tr><td>Tipo de producto</td><td>{{ $productoTipo }}</td></tr>
        <tr><td>Tipo de licencia</td><td>{{ $licenciaNombre }}</td></tr>
        <tr><td>Formato incluido</td><td>{{ $formato }}</td></tr>
        <tr><td>Precio pagado</td><td>{{ $precio }}</td></tr>
        <tr><td>Fecha de compra</td><td>{{ $fecha }}</td></tr>
        <tr><td>Número de compra / factura</td><td>{{ $numeroCompra }}</td></tr>
    </table>

    @if($isColeccion)
        <h2>Beats incluidos en la colección</h2>
        <table class="beats-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Género</th>
                    <th>BPM</th>
                    <th>Tono</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beatsIncluidos as $beat)
                    <tr>
                        <td>{{ $beat->titulo_beat }}</td>
                        <td>{{ $beat->genero_musical ?? '-' }}</td>
                        <td>{{ $beat->tempo_bpm ?? '-' }}</td>
                        <td>{{ $beat->tono_musical ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay beats asociados registrados en la colección.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="page-break"></div>

    <h2>Cláusulas contractuales</h2>

    <div class="clause">
        <h3>1. Objeto</h3>
        <p>
            El presente contrato tiene por objeto establecer los términos de la licencia concedida sobre
            {{ $isColeccion ? 'la colección musical y los beats incluidos en ella' : 'el beat identificado en los datos particulares' }}.
            La licencia permite al Licenciatario crear, grabar, sincronizar y distribuir una obra derivada
            que incorpore el producto licenciado, siempre dentro de los límites establecidos en este documento.
        </p>
    </div>

    <div class="clause">
        <h3>2. Naturaleza de la licencia</h3>
        <p>
            La licencia concedida es de carácter <strong>{{ $config['naturaleza'] }}</strong>.
            El Licenciante conserva la autoría, titularidad y derechos originales sobre la composición,
            producción, grabación instrumental y elementos sonoros que integran el producto.
        </p>
    </div>

    <div class="clause">
        <h3>3. Formatos entregados</h3>
        <p>
            El Licenciatario tendrá acceso a los formatos indicados en la compra: <strong>{{ $config['formatos'] }}</strong>.
            Cualquier formato adicional no expresamente incluido requerirá autorización previa o una nueva licencia.
        </p>
    </div>

    <div class="clause">
        <h3>4. Usos permitidos</h3>
        <p>
            El Licenciatario podrá utilizar el producto licenciado para grabar voces, mezclar, masterizar,
            publicar y distribuir una canción o pieza musical propia en plataformas digitales, redes sociales,
            páginas web, servicios de streaming, eventos y canales promocionales, respetando los límites
            cuantitativos de esta licencia.
        </p>
    </div>

    <h2>Límites de explotación</h2>
    <table class="limits-table">
        <tr><td>Reproducciones</td><td>{{ $config['reproducciones'] }}</td></tr>
        <tr><td>Copias / descargas</td><td>{{ $config['copias'] }}</td></tr>
        <tr><td>Videoclips</td><td>{{ $config['videoclips'] }}</td></tr>
        <tr><td>Actuaciones</td><td>{{ $config['actuaciones'] }}</td></tr>
        <tr><td>Monetización</td><td>{{ $config['monetizacion'] }}</td></tr>
        <tr><td>Exclusividad</td><td>{{ $config['exclusividad'] }}</td></tr>
    </table>

    <div class="page-break"></div>

    <div class="clause">
        <h3>5. Restricciones</h3>
        <p>
            El Licenciatario no podrá revender, sublicenciar, ceder, regalar, subir a bibliotecas de samples,
            redistribuir como instrumental independiente ni reclamar autoría exclusiva sobre el producto original.
            Tampoco podrá registrar el producto original en sistemas de identificación de contenido de forma
            que bloquee o perjudique al Licenciante o a otros licenciatarios legítimos.
        </p>
    </div>

    <div class="clause">
        <h3>6. Créditos</h3>
        <p>
            Salvo pacto escrito en contrario, el Licenciatario deberá acreditar al productor de forma razonable
            en publicaciones, descripciones, metadatos o materiales promocionales mediante una fórmula equivalente a:
            “Prod. by {{ $licencianteNombre }}”.
        </p>
    </div>

    <div class="clause">
        <h3>7. Propiedad intelectual</h3>
        <p>
            La compra de esta licencia no transmite la propiedad intelectual del producto original. El Licenciante
            mantiene todos los derechos no concedidos expresamente. El Licenciatario conserva los derechos sobre
            sus letras, interpretación vocal y aportaciones originales incorporadas a la obra derivada.
        </p>
    </div>

    <div class="clause">
        <h3>8. Distribución y monetización</h3>
        <p>
            La distribución y monetización quedan autorizadas únicamente dentro de los límites de la licencia.
            Si la explotación supera dichos límites, el Licenciatario deberá adquirir una licencia superior,
            renegociar condiciones o solicitar autorización expresa al Licenciante.
        </p>
    </div>

    <div class="clause">
        <h3>9. Garantía de disponibilidad</h3>
        <p>
            LevelBeats registra la operación y los datos esenciales de la licencia como evidencia funcional
            de la compra. El Licenciante garantiza que, en el momento de la publicación del producto en la
            plataforma, dispone de capacidad para conceder la licencia ofrecida.
        </p>
    </div>

    <div class="page-break"></div>

    <div class="clause">
        <h3>10. Condiciones específicas</h3>
        @if($tipoLicencia === 'exclusiva')
            <p>
                La licencia exclusiva concede al Licenciatario un derecho de explotación amplio sobre el producto
                identificado. Desde la formalización de la compra, el Licenciante no deberá vender nuevas licencias
                exclusivas sobre el mismo producto. Esta exclusividad no invalida licencias no exclusivas concedidas
                antes de la fecha de esta compra.
            </p>
        @else
            <p>
                La licencia no exclusiva permite al Licenciante seguir ofreciendo el mismo producto a otros usuarios
                bajo licencias iguales, superiores o distintas. El Licenciatario entiende que otros artistas pueden
                crear obras derivadas usando el mismo beat o colección.
            </p>
        @endif
    </div>

    <div class="clause">
        <h3>11. Incumplimiento</h3>
        <p>
            El incumplimiento de las condiciones de uso, límites de explotación, restricciones de redistribución
            o normas de crédito podrá dar lugar a la revocación de la licencia, reclamaciones de retirada de
            contenido y las acciones que correspondan según la normativa aplicable.
        </p>
    </div>

    <div class="clause">
        <h3>12. Alcance territorial y duración</h3>
        <p>
            La licencia tiene alcance internacional para usos digitales y físicos dentro de los límites indicados.
            Su vigencia se mantiene mientras el Licenciatario cumpla las condiciones pactadas y no exceda los
            límites de explotación definidos.
        </p>
    </div>

    <div class="clause">
        <h3>13. Plataforma LevelBeats</h3>
        <p>
            LevelBeats actúa como plataforma técnica de marketplace, registro de compra y generación documental.
            El documento se genera con los datos almacenados en el sistema en el momento de la transacción.
        </p>
    </div>

    <h2>Aceptación electrónica</h2>
    <div class="acceptance">
        <p>
            Al confirmar el pago de <strong>{{ $precio }}</strong> en LevelBeats, el Licenciatario declara haber
            leído y aceptado las condiciones de esta licencia. La aceptación electrónica queda asociada a
            <strong>{{ $numeroCompra }}</strong>, al usuario <strong>{{ $compradorNombre }}</strong> y al correo
            <strong>{{ $compradorEmail }}</strong>.
        </p>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line">
                    <strong>Licenciante</strong><br>
                    {{ $licencianteNombre }}
                </div>
            </td>
            <td>
                <div class="signature-line">
                    <strong>Licenciatario</strong><br>
                    {{ $compradorNombre }}<br>
                    Aceptación electrónica registrada
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
