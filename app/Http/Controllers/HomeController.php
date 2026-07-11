<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
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

        $kategorien = [
            'Alle', 'Feuerwehren', 'Rettungsdienste', 'Soziales & Integration',
            'Sport', 'Natur & Umwelt', 'Kultur', 'Bildung & Technik',
        ];

        $aktionen = collect([
            ['typ' => 'Aktion', 'titel' => 'Stammtisch für pflegende Angehörige', 'veranstalter' => 'Team Pflegenahversorgung', 'ort' => '9161 Maria Rain', 'zeit' => '14:00 Uhr', 'bild' => null, 'bild_alt' => null],
            ['typ' => 'Vortrag', 'titel' => 'Schlaganfall — was tun? Vortrag', 'veranstalter' => 'Ergotherapeutin Nicole Daumtschnig', 'ort' => '9161 Maria Rain', 'zeit' => '13:30 Uhr', 'bild' => null, 'bild_alt' => null],
            ['typ' => 'Bewegung', 'titel' => 'Lauftraining Bewegungstreff', 'veranstalter' => 'UNION LFL Köstenberg', 'ort' => '9231 Velden am Wörther See', 'zeit' => '18:00 Uhr', 'bild' => null, 'bild_alt' => null],
        ]);

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

        $stats = [
            'vereine' => '450+',
            'freiwillige' => '1.200',
            'stunden' => '15.000',
            'engagementFelder' => '7',
        ];

        return view('home', compact('vereine', 'kategorien', 'aktionen', 'benefits', 'testimonials', 'stats'));
    }

    public function impressum() { return view('impressum'); }
    public function datenschutz() { return view('datenschutz'); }
    public function barrierefreiheit() { return view('barrierefreiheit'); }
    public function inArbeit() { return view('in-arbeit'); }
}
