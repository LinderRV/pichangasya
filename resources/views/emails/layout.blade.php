<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('titulo', 'PichangasYa')</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Helvetica,Arial,sans-serif;color:#111827;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:8px;overflow:hidden;">
<tr>
    <td style="background:#198754;padding:20px 28px;">
        <span style="color:#ffffff;font-size:20px;font-weight:bold;">PichangasYa</span>
    </td>
</tr>
<tr>
    <td style="padding:28px;">
        @yield('contenido')
    </td>
</tr>
<tr>
    <td style="padding:16px 28px;border-top:1px solid #e5e7eb;text-align:center;">
        <span style="font-size:11px;color:#9ca3af;">Este es un mensaje automático, por favor no respondas a este correo.</span>
    </td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>
