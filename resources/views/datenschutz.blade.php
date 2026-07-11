@extends('layouts.app')

@section('title', 'Datenschutz – Freiwilligenzentrum Kärnten')
@section('description', 'Datenschutzerklärung des Freiwilligenzentrums Kärnten gemäß Art. 13/14 DSGVO.')

@section('content')
<div class="legal-page">
  <div class="container">
    <div class="box">
      <p class="placeholder-hint">⚠️ Platzhalter — vor Go-Live mit rechtsgeprüften Inhalten befüllen. Insbesondere Rechtsträger, Hosting-Anbieter, Aufbewahrungsfristen und ggf. Auftragsverarbeitungsvertrag eintragen.</p>
      <h1>Datenschutzerklärung</h1>
      <p>Zuletzt aktualisiert: [Datum]</p>

      <h2>1. Verantwortlicher</h2>
      <p>[Name des Rechtsträgers], [Adresse], [PLZ Ort] — E-Mail: [office@…]</p>

      <h2>2. Welche Daten wir verarbeiten</h2>
      <p>Beim Besuch dieser Website verarbeiten wir folgende Daten:</p>
      <ul>
        <li>Technisch notwendige Daten (IP-Adresse, Browser-Typ, Zugriffszeit) durch unseren Hosting-Anbieter [Anbieter]</li>
        <li>Formulardaten, die Sie freiwillig übermitteln (z. B. Vereinsregistrierung)</li>
        <li>Cookie-Einwilligungsentscheidung (gespeichert lokal im Browser, kein Server-Transfer)</li>
      </ul>

      <h2>3. Rechtsgrundlagen</h2>
      <p>Die Verarbeitung erfolgt auf Basis von Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung), lit. c (rechtliche Verpflichtung) und lit. f (berechtigte Interessen) sowie bei optionalen Cookies auf Basis Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO).</p>

      <h2>4. Cookies</h2>
      <p>Wir setzen ausschließlich technisch notwendige Cookies ein. Optionale Analyse- und Marketing-Cookies werden nur nach Ihrer ausdrücklichen Einwilligung aktiviert. Ihre Einwilligung können Sie jederzeit über den Link „Cookie-Einstellungen" im Footer widerrufen (Art. 7 Abs. 3 DSGVO).</p>

      <h2>5. Ihre Rechte</h2>
      <p>Sie haben das Recht auf Auskunft (Art. 15), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung (Art. 18), Datenübertragbarkeit (Art. 20) und Widerspruch (Art. 21 DSGVO). Zur Ausübung Ihrer Rechte wenden Sie sich an: [E-Mail].</p>
      <p>Sie haben zudem das Recht, Beschwerde bei der österreichischen Datenschutzbehörde einzulegen: <a href="https://www.dsb.gv.at" rel="noopener noreferrer" target="_blank">www.dsb.gv.at</a></p>

      <h2>6. Hosting</h2>
      <p>Diese Website wird gehostet bei [Hosting-Anbieter, Adresse]. Mit dem Anbieter besteht ein Auftragsverarbeitungsvertrag gemäß Art. 28 DSGVO.</p>

      <p style="margin-top:2rem"><a href="{{ url('/') }}">&larr; Zurück zur Startseite</a></p>
    </div>
  </div>
</div>
@endsection
