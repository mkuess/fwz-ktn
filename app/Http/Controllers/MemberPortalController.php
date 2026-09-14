<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    public function index(): View
    {
        $member = auth('member')->user()->load('organisation');
        if (empty($member->membership_number)) {
            $member->update([
                'membership_number' => 'FWZ-'.now()->year.'-'.str_pad($member->id, 6, '0', STR_PAD_LEFT),
            ]);
            $member->refresh();
        }

        $benefits = Benefit::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('mein-bereich.index', compact('member', 'benefits'));
    }

    public function edit(): View
    {
        $member = auth('member')->user()->load('organisation');

        return view('mein-bereich.edit', compact('member'));
    }

    public function update(Request $request): RedirectResponse
    {
        $member = auth('member')->user();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:4'],
            'city' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:member'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'Bitte gib deinen Vornamen an.',
            'last_name.required' => 'Bitte gib deinen Nachnamen an.',
            'street.required' => 'Bitte gib deine Straße und Hausnummer an.',
            'zip.required' => 'Bitte gib deine Postleitzahl an.',
            'zip.max' => 'Die Postleitzahl darf maximal 4 Stellen haben.',
            'city.required' => 'Bitte gib deinen Ort an.',
            'current_password.required_with' => 'Bitte gib dein aktuelles Passwort ein.',
            'current_password.current_password' => 'Das aktuelle Passwort ist nicht korrekt.',
            'password.min' => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die neuen Passwörter stimmen nicht überein.',
        ]);

        $member->update(array_filter([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'street' => $validated['street'],
            'zip' => $validated['zip'],
            'city' => $validated['city'],
            'password' => $validated['password'] ?? null,
        ], fn (mixed $value): bool => $value !== null));

        return redirect()
            ->route('member.portal')
            ->with('success', 'Deine Daten wurden aktualisiert.');
    }

    public function benefit(int $id): View
    {
        $benefit = Benefit::where('id', $id)->where('is_active', true)->firstOrFail();
        $member = auth('member')->user()->load('organisation');

        return view('mein-bereich.benefit', compact('benefit', 'member'));
    }
}
