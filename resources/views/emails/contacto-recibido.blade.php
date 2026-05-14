<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f6;color:#111;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f6;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #dddddf;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px 28px;background:#0b0b10;color:#ffffff;">
                            <h1 style="margin:0;font-size:22px;line-height:1.2;">Nuevo mensaje de contacto</h1>
                            <p style="margin:8px 0 0;color:#cfcfd6;font-size:14px;">LevelBeats</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0;color:#666;width:150px;">Nombre</td>
                                    <td style="padding:8px 0;color:#111;font-weight:bold;">{{ $nombre }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#666;">Email</td>
                                    <td style="padding:8px 0;color:#111;">{{ $email }}</td>
                                </tr>
                                @if($asunto)
                                    <tr>
                                        <td style="padding:8px 0;color:#666;">Asunto</td>
                                        <td style="padding:8px 0;color:#111;">{{ $asunto }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:8px 0;color:#666;">Fecha</td>
                                    <td style="padding:8px 0;color:#111;">{{ now()->timezone('Europe/Madrid')->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#666;">IP</td>
                                    <td style="padding:8px 0;color:#111;">{{ $ip ?? 'No disponible' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#666;">User-Agent</td>
                                    <td style="padding:8px 0;color:#111;">{{ $userAgent ?: 'No disponible' }}</td>
                                </tr>
                            </table>

                            <div style="margin-top:22px;padding:18px;background:#f7f7f9;border:1px solid #e6e6ea;border-radius:10px;">
                                <h2 style="margin:0 0 10px;font-size:16px;color:#111;">Mensaje</h2>
                                <p style="margin:0;color:#222;line-height:1.6;white-space:pre-line;">{{ $mensaje }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
