<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Startseite. Die Arrays unten enthalten exakt die Demo-Inhalte aus der
     * ursprünglichen Design-Vorlage, nur jetzt als Datenstruktur statt fest
     * codiertem HTML. Jeder Block zeigt per TODO-Kommentar, wie die spätere
     * echte Eloquent-Abfrage aussehen sollte.
     */
    public function index(Request $request)
    {
        // TODO: ersetzen durch z. B.
        // Verein::query()
        //   ->where('freigeschaltet', true)
        //   ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
        //   ->when($request->filled('ort'), fn ($q) => $q->where('ort', 'like', '%'.$request->ort.'%'))
        //   ->orderBy('name')->get()
        //   ->map(fn ($v) => ['name' => $v->name, 'ort' => $v->ort, 'kuerzel' => $v->kuerzel]);
        $vereine = collect([
            ['kuerzel' => 'ÖRK', 'name' => 'Österreichisches Rotes Kreuz Kärnten', 'ort' => 'Klagenfurt'],
            ['kuerzel' => 'ÖWR', 'name' => 'Österreichische Wasserrettung Kärnten', 'ort' => 'Klagenfurt'],
            ['kuerzel' => 'FFV', 'name' => 'Freiwillige Feuerwehr Villach', 'ort' => 'Villach'],
            ['kuerzel' => 'ASKÖ', 'name' => 'ASKÖ Landesverband Kärnten', 'ort' => 'Klagenfurt'],
            ['kuerzel' => 'BRK', 'name' => 'Bergrettung Kärnten', 'ort' => 'Spittal an der Drau'],
            ['kuerzel' => 'CK', 'name' => 'Caritas Kärnten', 'ort' => 'Klagenfurt'],
            ['kuerzel' => 'NFK', 'name' => 'Naturfreunde Kärnten', 'ort' => 'Villach'],
            ['kuerzel' => 'LB', 'name' => 'Lese-Buddies Kärnten', 'ort' => 'Villach'],
        ]);

        // TODO: ersetzen durch z. B. Kategorie::pluck('name')->prepend('Alle')
        $kategorien = ['Alle', 'Feuerwehren', 'Rettungsdienste', 'Soziales & Integration', 'Sport', 'Natur & Umwelt', 'Kultur', 'Bildung & Technik'];

        // TODO: ersetzen durch z. B. Aktion::upcoming()->take(3)->get()
        // Die Bild-Dateinamen (1.avif / 3.avif / 4.avif) und Alt-Texte stammen 1:1
        // aus der Vorlage und sind aktuell Platzhalter (echte Fotos folgen).
        $aktionen = collect([
            [
                'typ' => 'Aktion',
                'titel' => 'Stammtisch für pflegende Angehörige',
                'veranstalter' => 'Team Pflegenahversorgung',
                'ort' => '9161 Maria Rain',
                'zeit' => '14:00 Uhr',
                'bild' => asset('img/4.avif'),
                'bild_alt' => 'Gemeinschaftliche Unterstützung und Austausch',
            ],
            [
                'typ' => 'Vortrag',
                'titel' => 'Schlaganfall — was tun? Vortrag',
                'veranstalter' => 'Ergotherapeutin Nicole Daumtschnig',
                'ort' => '9161 Maria Rain',
                'zeit' => '13:30 Uhr',
                'bild' => asset('img/3.avif'),
                'bild_alt' => 'Vortrag und Gesundheitsinformation',
            ],
            [
                'typ' => 'Bewegung',
                'titel' => 'Lauftraining Bewegungstreff',
                'veranstalter' => 'UNION LFL Köstenberg',
                'ort' => '9231 Velden am Wörther See',
                'zeit' => '18:00 Uhr',
                'bild' => asset('img/1.avif'),
                'bild_alt' => 'Lauftraining in der Gruppe',
            ],
        ]);

        // TODO: ersetzen durch z. B. Benefit::active()->get()
        $benefits = collect([
            ['partner' => 'RUTAR', 'beschreibung' => '10 % Exklusiv-Rabatt auf fast alles.', 'code' => 'mit Code: FWZ10'],
            ['partner' => 'FEICHTINGER', 'beschreibung' => '10 % auf ausgewählte Uhren und 20 % auf Schmuck & Eheringe.', 'code' => 'mit Code: FWZSPORT10'],
            ['partner' => 'ARBÖ', 'beschreibung' => '€ 25,– Gutschein auf verschiedene Dienstleistungen und Clubleistungen.', 'code' => 'Mitgliedervorteil'],
        ]);

        $testimonials = [
            ['zitat' => 'Beim Roten Kreuz finde ich Sinn und Gemeinschaft. Jeder Einsatz macht einen Unterschied.', 'person' => 'Maria L.', 'rolle' => 'ÖRK Kärnten'],
            ['zitat' => 'Die Bergrettung ist mehr als ein Hobby — sie ist Verantwortung, Teamgeist und Dankbarkeit.', 'person' => 'Thomas R.', 'rolle' => 'Bergrettung Kärnten'],
            ['zitat' => 'Im Verein begleiten wir Kinder beim Aufwachsen und schaffen Chancen für alle.', 'person' => 'Sophie K.', 'rolle' => 'Naturfreunde Kärnten'],
        ];

        // TODO: ersetzen durch echte Aggregation, z. B.
        // 'vereine' => Verein::where('freigeschaltet', true)->count().'+',
        $stats = [
            'vereine' => '450+',
            'freiwillige' => '1.2k',
            'stunden' => '15k',
            'engagementFelder' => '7',
        ];

        return view('home', compact('vereine', 'kategorien', 'aktionen', 'benefits', 'testimonials', 'stats'));
    }
}
