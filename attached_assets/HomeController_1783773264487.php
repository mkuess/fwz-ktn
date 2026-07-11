<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Startseite mit allen dynamischen Bereichen.
     *
     * WICHTIG: Die Arrays unten sind bewusst als einfache PHP-Arrays gebaut, damit
     * die Seite sofort lauffähig ist, auch bevor die zugehörigen Filament-Ressourcen /
     * Eloquent-Modelle existieren. Sobald ihr die Models habt (z. B. Verein, Aktion,
     * Benefit), ersetzt einfach die markierten Blöcke durch echte Queries – die Blade-
     * Views (`resources/views/home.blade.php`) erwarten exakt diese Datenstruktur.
     */
    public function index(Request $request)
    {
        // ---------------------------------------------------------------
        // TODO: durch echte Query ersetzen, z. B.:
        // $vereine = Verein::query()
        //     ->where('freigeschaltet', true)
        //     ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
        //     ->when($request->filled('ort'), fn ($q) => $q->where('ort', 'like', '%'.$request->ort.'%'))
        //     ->when($request->filled('kategorie') && $request->kategorie !== 'Alle',
        //         fn ($q) => $q->where('kategorie', $request->kategorie))
        //     ->orderBy('name')
        //     ->get()
        //     ->map(fn ($v) => [
        //         'name'    => $v->name,
        //         'ort'     => $v->ort,
        //         'kuerzel' => $v->kuerzel,
        //     ]);
        $vereine = collect([
            ['name' => 'Österreichisches Rotes Kreuz Kärnten', 'ort' => 'Klagenfurt', 'kuerzel' => 'ÖRK'],
            ['name' => 'Österreichische Wasserrettung Kärnten', 'ort' => 'Klagenfurt', 'kuerzel' => 'ÖWR'],
            ['name' => 'Freiwillige Feuerwehr Villach', 'ort' => 'Villach', 'kuerzel' => 'FFV'],
            ['name' => 'ASKÖ Landesverband Kärnten', 'ort' => 'Klagenfurt', 'kuerzel' => 'ASKÖ'],
            ['name' => 'Bergrettung Kärnten', 'ort' => 'Spittal an der Drau', 'kuerzel' => 'BRK'],
            ['name' => 'Caritas Kärnten', 'ort' => 'Klagenfurt', 'kuerzel' => 'CK'],
            ['name' => 'Naturfreunde Kärnten', 'ort' => 'Villach', 'kuerzel' => 'NFK'],
            ['name' => 'Lese-Buddies Kärnten', 'ort' => 'Villach', 'kuerzel' => 'LB'],
        ]);

        // TODO: z. B. Kategorie::pluck('name')->prepend('Alle') aus dem Backend
        $kategorien = [
            'Alle', 'Feuerwehren', 'Rettungsdienste', 'Soziales & Integration',
            'Sport', 'Natur & Umwelt', 'Kultur', 'Bildung & Technik',
        ];

        // TODO: durch echte Query ersetzen, z. B. Aktion::upcoming()->take(3)->get()
        $aktionen = collect([
            [
                'typ' => 'Aktion',
                'titel' => 'Stammtisch für pflegende Angehörige',
                'veranstalter' => 'Team Pflegenahversorgung',
                'ort' => '9161 Maria Rain',
                'zeit' => '14:00 Uhr',
                'bild' => null, // null => Platzhalterbild wird im View verwendet
                'bild_alt' => null,
            ],
            [
                'typ' => 'Vortrag',
                'titel' => 'Schlaganfall — was tun? Vortrag',
                'veranstalter' => 'Ergotherapeutin Nicole Daumtschnig',
                'ort' => '9161 Maria Rain',
                'zeit' => '13:30 Uhr',
                'bild' => null,
                'bild_alt' => null,
            ],
            [
                'typ' => 'Bewegung',
                'titel' => 'Lauftraining Bewegungstreff',
                'veranstalter' => 'UNION LFL Köstenberg',
                'ort' => '9231 Velden am Wörther See',
                'zeit' => '18:00 Uhr',
                'bild' => null,
                'bild_alt' => null,
            ],
        ]);

        // TODO: durch echte Query ersetzen, z. B. Benefit::active()->get()
        $benefits = collect([
            ['partner' => 'RUTAR', 'beschreibung' => '10 % Exklusiv-Rabatt auf fast alles.', 'code' => 'mit Code: FWZ10'],
            ['partner' => 'FEICHTINGER', 'beschreibung' => '10 % auf ausgewählte Uhren und 20 % auf Schmuck & Eheringe.', 'code' => 'mit Code: FWZSPORT10'],
            ['partner' => 'ARBÖ', 'beschreibung' => '€ 25,– Gutschein auf verschiedene Dienstleistungen und Clubleistungen.', 'code' => 'Mitgliedervorteil'],
        ]);

        // Testimonials bleiben i. d. R. redaktionell fix – bei Bedarf ebenfalls ins Backend auslagern.
        $testimonials = [
            ['zitat' => 'Beim Roten Kreuz finde ich Sinn und Gemeinschaft. Jeder Einsatz macht einen Unterschied.', 'person' => 'Maria L.', 'rolle' => 'ÖRK Kärnten'],
            ['zitat' => 'Die Bergrettung ist mehr als ein Hobby — sie ist Verantwortung, Teamgeist und Dankbarkeit.', 'person' => 'Thomas R.', 'rolle' => 'Bergrettung Kärnten'],
            ['zitat' => 'Im Verein begleiten wir Kinder beim Aufwachsen und schaffen Chancen für alle.', 'person' => 'Sophie K.', 'rolle' => 'Naturfreunde Kärnten'],
        ];

        // TODO: echte Kennzahlen aus dem Backend aggregieren, z. B.:
        // $stats = [
        //     'vereine' => Verein::where('freigeschaltet', true)->count().'+',
        //     'freiwillige' => number_format(Freiwilliger::count(), 0, ',', '.'),
        //     'stunden' => number_format(Einsatz::sum('stunden'), 0, ',', '.'),
        //     'engagementFelder' => Kategorie::count(),
        // ];
        $stats = [
            'vereine' => '450+',
            'freiwillige' => '1.200',
            'stunden' => '15.000',
            'engagementFelder' => '7',
        ];

        return view('home', compact('vereine', 'kategorien', 'aktionen', 'benefits', 'testimonials', 'stats'));
    }
}
