<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>Neue Organisation registriert</title></head>
<body style="font-family:Arial,sans-serif;background:#f3f7fc;padding:40px 0;margin:0">
  <div style="max-width:580px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #d7e2ef">
    <div style="background:#08264f;padding:28px 32px">
      <p style="color:#fff;font-size:1.25rem;font-weight:800;margin:0">FWZ Kärnten — Neue Organisation</p>
    </div>
    <div style="padding:32px">
      <p style="color:#37506d;margin:0 0 20px">Eine neue Organisation hat sich registriert und wartet auf Freischaltung:</p>
      <table style="width:100%;border-collapse:collapse;font-size:.95rem">
        <tr>
          <td style="padding:10px 14px;border:1px solid #d7e2ef;background:#f3f7fc;font-weight:700;width:38%">Name</td>
          <td style="padding:10px 14px;border:1px solid #d7e2ef">{{ $organisation->name }}</td>
        </tr>
        <tr>
          <td style="padding:10px 14px;border:1px solid #d7e2ef;background:#f3f7fc;font-weight:700">Typ</td>
          <td style="padding:10px 14px;border:1px solid #d7e2ef">{{ $organisation->type === 'verein' ? 'Verein' : 'Organisation' }}</td>
        </tr>
        <tr>
          <td style="padding:10px 14px;border:1px solid #d7e2ef;background:#f3f7fc;font-weight:700">E-Mail</td>
          <td style="padding:10px 14px;border:1px solid #d7e2ef">{{ $organisation->email }}</td>
        </tr>
        @if($organisation->zvr_number)
        <tr>
          <td style="padding:10px 14px;border:1px solid #d7e2ef;background:#f3f7fc;font-weight:700">ZVR-Nummer</td>
          <td style="padding:10px 14px;border:1px solid #d7e2ef">{{ $organisation->zvr_number }}</td>
        </tr>
        @endif
        @if($organisation->city)
        <tr>
          <td style="padding:10px 14px;border:1px solid #d7e2ef;background:#f3f7fc;font-weight:700">Ort</td>
          <td style="padding:10px 14px;border:1px solid #d7e2ef">{{ $organisation->zip }} {{ $organisation->city }}</td>
        </tr>
        @endif
      </table>
      <div style="margin-top:28px">
        <a href="{{ url('/verwaltung') }}" style="display:inline-block;background:#0b3165;color:#fff;padding:12px 24px;border-radius:999px;font-weight:800;text-decoration:none">Im Admin-Panel freischalten →</a>
      </div>
      <p style="margin-top:28px;font-size:.88rem;color:#5d7390">Diese Nachricht wurde automatisch vom FWZ-Registrierungsformular gesendet.</p>
    </div>
  </div>
</body>
</html>
