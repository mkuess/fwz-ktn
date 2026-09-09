<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Zugang freigeschaltet</title>
</head>
<body style="margin:0;padding:24px;background:#f3f7fc;font-family:Arial,Helvetica,sans-serif;color:#111111">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:16px;padding:32px">
        <img src="{{ asset('images/fwz_logo.svg') }}" alt="Freiwilligenzentrum Kärnten" style="display:block;width:220px;max-width:100%;height:auto;margin-bottom:28px">
        <h1 style="font-size:24px;margin:0 0 16px">Dein Account wurde freigeschaltet</h1>
        <p style="font-size:16px;line-height:1.6;margin:0 0 20px">
            Dein Zugang zum Freiwilligenzentrum Kärnten wurde geprüft und freigeschaltet.
            Mit diesem Code kannst du jetzt dein Passwort festlegen:
        </p>
        <p style="font-family:monospace;font-size:36px;font-weight:700;letter-spacing:0.18em;margin:0 0 24px;color:#111111">
            {{ $code }}
        </p>
        <p style="margin:0 0 24px">
            <a href="{{ $resetUrl }}" style="display:inline-block;background:#e57b00;color:#ffffff;padding:12px 22px;border-radius:8px;font-weight:700;text-decoration:none">
                Code eingeben und Passwort festlegen
            </a>
        </p>
        <p style="font-size:14px;line-height:1.6;color:#4b5563;margin:0">
            Der Freischaltcode und der Link sind {{ $lifetimeDays }} Tage gültig.
            Wenn du dich nicht registriert hast, kannst du diese E-Mail ignorieren.
        </p>
    </div>
</body>
</html>