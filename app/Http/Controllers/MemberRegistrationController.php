<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberRegistrationController extends Controller
{
    public function show()
    {
        $organisations = Organisation::where('is_approved', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        $preselectedOrg = request('organisation');

        return view('mitglied-werden.index', compact('organisations', 'preselectedOrg'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:members,email',
            'street' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'confirm_membership' => 'required|accepted',
            'confirm_privacy' => 'required|accepted',
            'newsletter_optin' => 'nullable|boolean',
        ], [
            'organisation_id.required' => 'Bitte wähle eine Organisation aus.',
            'organisation_id.exists' => 'Die gewählte Organisation ist ungültig.',
            'first_name.required' => 'Bitte gib deinen Vornamen an.',
            'last_name.required' => 'Bitte gib deinen Nachnamen an.',
            'email.required' => 'Bitte gib deine E-Mail-Adresse an.',
            'email.email' => 'Bitte gib eine gültige E-Mail-Adresse an.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
            'street.required' => 'Bitte gib deine Straße und Hausnummer an.',
            'zip.required' => 'Bitte gib deine Postleitzahl an.',
            'city.required' => 'Bitte gib deinen Ort an.',
            'confirm_membership.required' => 'Bitte bestätige deine Vereinszugehörigkeit.',
            'confirm_membership.accepted' => 'Bitte bestätige deine Vereinszugehörigkeit.',
            'confirm_privacy.required' => 'Bitte stimme der Datenschutzerklärung zu.',
            'confirm_privacy.accepted' => 'Bitte stimme der Datenschutzerklärung zu.',
        ]);

        Member::create([
            'organisation_id' => $validated['organisation_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'street' => $validated['street'] ?? null,
            'zip' => $validated['zip'] ?? null,
            'city' => $validated['city'] ?? null,
            'newsletter_optin' => $request->boolean('newsletter_optin'),
            'status' => 'pending',
            'source' => 'self',
            'role' => 'member',
            'password' => bcrypt(Str::random(32)),
        ]);

        return redirect()->route('member.register.danke');
    }

    public function danke()
    {
        return view('mitglied-werden.danke');
    }
}
