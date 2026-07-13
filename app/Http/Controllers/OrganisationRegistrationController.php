<?php

namespace App\Http\Controllers;

use App\Mail\OrgRegistered;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrganisationRegistrationController extends Controller
{
    /* ── Step 1: Organisation ─────────────────────────────────────────── */

    public function schritt1()
    {
        return view('registrierung.schritt1', ['step' => 1, 'old' => session('reg_data', [])]);
    }

    public function schritt1Post(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:verein,organisation'],
            'zvr_number'  => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo'        => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('vereinslogos', 'public');
        }
        unset($data['logo']);

        $reg = array_merge(session('reg_data', []), $data);
        session(['reg_data' => $reg]);

        return redirect()->route('registrierung.schritt2');
    }

    /* ── Step 2: Benutzerkonto ────────────────────────────────────────── */

    public function schritt2()
    {
        if (! session()->has('reg_data.name')) {
            return redirect()->route('registrierung.schritt1');
        }
        return view('registrierung.schritt2', ['step' => 2, 'old' => session('reg_data', [])]);
    }

    public function schritt2Post(Request $request)
    {
        if (! session()->has('reg_data.name')) {
            return redirect()->route('registrierung.schritt1');
        }

        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255', 'unique:organisations,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $reg = array_merge(session('reg_data', []), $data);
        session(['reg_data' => $reg]);

        return redirect()->route('registrierung.schritt3');
    }

    /* ── Step 3: Standort & Kontakt ───────────────────────────────────── */

    public function schritt3()
    {
        if (! session()->has('reg_data.email')) {
            return redirect()->route('registrierung.schritt1');
        }
        return view('registrierung.schritt3', ['step' => 3, 'old' => session('reg_data', [])]);
    }

    public function schritt3Post(Request $request)
    {
        if (! session()->has('reg_data.email')) {
            return redirect()->route('registrierung.schritt1');
        }

        $data = $request->validate([
            'street'           => ['nullable', 'string', 'max:255'],
            'zip'              => ['nullable', 'string', 'max:10'],
            'city'             => ['nullable', 'string', 'max:100'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'website'          => ['nullable', 'url', 'max:255'],
            'representative'   => ['nullable', 'string', 'max:255'],
            'contact_person'   => ['nullable', 'string', 'max:255'],
        ]);

        $reg = array_merge(session('reg_data', []), $data);

        $organisation = Organisation::create([
            'name'           => $reg['name'],
            'type'           => $reg['type'],
            'role'           => 'org_admin',
            'zvr_number'     => $reg['zvr_number'] ?? null,
            'description'    => $reg['description'] ?? null,
            'logo_path'      => $reg['logo_path'] ?? null,
            'email'          => $reg['email'],
            'password'       => $reg['password'],
            'street'         => $reg['street'] ?? null,
            'zip'            => $reg['zip'] ?? null,
            'city'           => $reg['city'] ?? null,
            'phone'          => $reg['phone'] ?? null,
            'website'        => $reg['website'] ?? null,
            'representative' => $reg['representative'] ?? null,
            'contact_person' => $reg['contact_person'] ?? null,
            'is_approved'    => false,
            'is_active'      => true,
        ]);

        try {
            Mail::to(config('mail.fwz_admin', 'office@freiwilligenzentrum-kaernten.at'))
                ->send(new OrgRegistered($organisation));
        } catch (\Throwable $e) {
            // Mail failure must not block registration
            logger()->error('OrgRegistered mail failed: ' . $e->getMessage());
        }

        session()->forget('reg_data');

        return redirect()->route('registrierung.danke');
    }

    /* ── Danke ────────────────────────────────────────────────────────── */

    public function danke()
    {
        return view('registrierung.danke');
    }
}
