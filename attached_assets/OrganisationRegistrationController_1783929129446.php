<?php

namespace App\Http\Controllers;

use App\Mail\OrgRegistered;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * 3-Schritt-Registrierung für Vereine/Organisationen, entsprechend Kapitel 5
 * des Konzeptdokuments. Zwischendaten liegen in der Session (Schlüssel
 * "registrierung"), erst beim finalen Absenden in Schritt 3 wird der
 * Organisation-Datensatz angelegt (is_approved=false, Freischaltung erfolgt
 * im Filament-Backend).
 */
class OrganisationRegistrationController extends Controller
{
    private const SESSION_KEY = 'registrierung';

    public function schritt1()
    {
        return view('registrierung.schritt1', [
            'daten' => session(self::SESSION_KEY, []),
        ]);
    }

    public function schritt1Speichern(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:verein,organisation'],
            'zvr_number' => ['required_if:type,verein', 'nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:organisations,email'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('logo')) {
            // Wird erst nach vollständigem Absenden dauerhaft gespeichert (siehe schritt3Speichern),
            // hier landet die Datei zunächst temporär im "local"-Storage.
            $validated['logo_temp_path'] = $request->file('logo')->store('registrierung-tmp', 'local');
        }
        unset($validated['logo']);

        $this->mergeSession($validated);

        return redirect()->route('registrierung.schritt2');
    }

    public function schritt2()
    {
        $daten = session(self::SESSION_KEY, []);
        if (empty($daten['name']) || empty($daten['email'])) {
            return redirect()->route('registrierung.schritt1');
        }

        return view('registrierung.schritt2', ['daten' => $daten]);
    }

    public function schritt2Speichern(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'description' => ['required', 'string', 'max:2000'],
            // Bewusst OHNE vorangehaktes Häkchen im Formular (siehe schritt2.blade.php) —
            // vorangehakte Einwilligungen sind nach DSGVO (Art. 4 Nr. 11, Art. 7) unwirksam.
            'newsletter_optin' => ['nullable', 'boolean'],
        ]);
        $validated['newsletter_optin'] = $request->boolean('newsletter_optin');

        $this->mergeSession($validated);

        return redirect()->route('registrierung.schritt3');
    }

    public function schritt3()
    {
        $daten = session(self::SESSION_KEY, []);
        if (empty($daten['password'])) {
            return redirect()->route('registrierung.schritt1');
        }

        return view('registrierung.schritt3', ['daten' => $daten]);
    }

    public function schritt3Speichern(Request $request)
    {
        $validated = $request->validate([
            'street' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'representative' => ['required', 'string', 'max:255'],
            'same_contact' => ['nullable', 'boolean'],
            'contact_person' => ['required_unless:same_contact,1', 'nullable', 'string', 'max:255'],
        ]);

        $daten = array_merge(session(self::SESSION_KEY, []), $validated);
        $daten['contact_person'] = $request->boolean('same_contact')
            ? $daten['representative']
            : $validated['contact_person'];

        // Logo aus dem temporären Storage in den dauerhaften "public"-Datenträger verschieben.
        $logoPath = null;
        if (! empty($daten['logo_temp_path'])) {
            $logoPath = str_replace('registrierung-tmp/', 'vereinslogos/', $daten['logo_temp_path']);
            Storage::disk('public')->put($logoPath, Storage::disk('local')->get($daten['logo_temp_path']));
            Storage::disk('local')->delete($daten['logo_temp_path']);
        }

        $organisation = Organisation::create([
            'type' => $daten['type'],
            'name' => $daten['name'],
            'zvr_number' => $daten['zvr_number'] ?? null,
            'email' => $daten['email'],
            'password' => $daten['password'],
            'description' => $daten['description'],
            'logo_path' => $logoPath,
            'street' => $daten['street'],
            'zip' => $daten['zip'],
            'city' => $daten['city'],
            'phone' => $daten['phone'] ?? null,
            'website' => $daten['website'] ?? null,
            'representative' => $daten['representative'],
            'contact_person' => $daten['contact_person'],
            'newsletter_optin' => $daten['newsletter_optin'] ?? false,
            'is_registered' => true,
            'is_approved' => false,
            'is_active' => true,
        ]);

        // TODO: Admin-E-Mail-Adresse konfigurieren (z. B. in config/mail.php oder .env als
        // FWZ_ADMIN_EMAIL), aktuell Platzhalter in App\Mail\OrgRegistered.
        Mail::to(config('mail.fwz_admin_address', 'office@freiwilligenzentrum-kaernten.at'))
            ->send(new OrgRegistered($organisation));

        session()->forget(self::SESSION_KEY);

        return redirect()->route('registrierung.danke');
    }

    public function danke()
    {
        return view('registrierung.danke');
    }

    private function mergeSession(array $data): void
    {
        session([self::SESSION_KEY => array_merge(session(self::SESSION_KEY, []), $data)]);
    }
}
