<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CollabOrbit — Подтверждение email</title>
</head>
<body style="margin:0;padding:0;background:#1d2021;font-family:monospace;color:rgba(248,250,252,0.88);">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#1d2021; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" border="0">
                <!-- Header -->
                <tr>
                    <td style="padding:16px 0; text-align:left;">
                        <a style="text-decoration:none; display:flex; align-items:center; gap:8px;">
                            <span style="font-size:14px;font-weight:600;color:rgba(248,250,252,0.88);">CollabOrbit</span>
                        </a>
                    </td>
                </tr>

                <!-- Card -->
                <tr>
                    <td style="background:rgba(255,255,255,0.03);border:1px solid rgba(214,93,14,0.18);border-radius:10px;padding:24px;color:rgba(248,250,252,0.88);">
                        <!-- Status Badge -->
                        <p style="display:inline-block;padding:4px 12px;border:1px solid rgba(214,93,14,0.18);border-radius:999px;font-size:12px;color:rgba(248,250,252,0.56);margin-bottom:16px;">
                            <span style="display:inline-block;width:6px;height:6px;background:rgba(214,93,14,0.75);border-radius:999px;margin-right:6px;"></span>
                            Подтвердите email, чтобы завершить регистрацию
                        </p>

                        <!-- Title -->
                        <h1 style="font-size:24px;font-weight:600;line-height:1.2;margin:0 0 16px 0;">Добро пожаловать в CollabOrbit, {{ $user->first_name }}</h1>

                        <!-- Text -->
                        <p style="font-size:15px;line-height:1.5;color:rgba(248,250,252,0.72);margin:0 0 24px 0;">
                            Нажмите кнопку ниже, чтобы подтвердить электронную почту и получить доступ к рабочему пространству.
                        </p>

                        <!-- Button -->
                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $url }}" style="display:inline-block;padding:10px 20px;background:rgba(214,93,14,0.14);border:1px solid rgba(214,93,14,0.34);border-radius:8px;color:rgba(248,250,252,0.92);text-decoration:none;font-weight:600;">Подтвердить email</a>
                                </td>
                            </tr>
                        </table>

                        <!-- Fallback URL -->
                        <p style="font-size:12px;color:rgba(248,250,252,0.56);margin:0 0 4px 0;">Если кнопка не сработала, вставьте ссылку в браузер:</p>
                        <p style="font-size:12px;color:rgba(248,250,252,0.56);word-break:break-all;margin:0 0 16px 0;">{{ $url }}</p>

                        <!-- Note -->
                        <p style="font-size:12px;color:rgba(248,250,252,0.56);margin:0;">Если вы не запрашивали этот код, просто пропустите письмо.</p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:24px 0;text-align:center;font-size:12px;color:rgba(248,250,252,0.56);">
                        © 2026 CollabOrbit. Built for focused teams.<br>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
