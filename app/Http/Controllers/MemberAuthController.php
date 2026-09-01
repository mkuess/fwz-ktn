<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberAuthController extends Controller
{
    public function showLogin()
    {
        if (auth('member')->check()) {
            return redirect()->route('member.portal');
        }

        return view('auth.member-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
            'password.required' => 'Bitte gib dein Passwort ein.',
        ]);

        if (auth('member')->attempt($credentials, $request->boolean('remember'))) {
            $member = auth('member')->user();
            if ($member->role === 'admin') {
                auth('member')->logout();

                return back()->withErrors(['email' => 'FWZ-Admins melden sich ausschließlich unter /verwaltung an.']);
            }
            if ($member->status !== 'approved') {
                auth('member')->logout();

                return back()->withErrors(['email' => 'Dein Konto wurde noch nicht freigeschaltet.']);
            }
            $request->session()->regenerate();

            return redirect()->route('member.portal');
        }

        return back()->withErrors(['email' => 'E-Mail oder Passwort ist falsch.'])->withInput();
    }

    public function logout(Request $request)
    {
        auth('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }

    public function showForgotPassword()
    {
        return view('auth.member-forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $member = Member::where('email', $request->email)->first();
        if ($member) {
            $token = Str::random(64);
            $member->update([
                'activation_token' => $token,
                'activation_sent_at' => now(),
            ]);
            session(['reset_link' => url('/aktivierung/'.$token)]);
        }

        return back()->with('status', 'Falls ein Konto mit dieser E-Mail existiert, wurde ein Link gesendet.');
    }

    public function showActivation(string $token)
    {
        $member = Member::where('activation_token', $token)->firstOrFail();

        return view('aktivierung.index', compact('member', 'token'));
    }

    public function activate(Request $request, string $token)
    {
        $member = Member::where('activation_token', $token)->firstOrFail();
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Bitte wähle ein Passwort.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
        ]);
        $member->update([
            'password' => bcrypt($request->password),
            'activation_token' => null,
            'status' => 'approved',
        ]);

        if ($member->role === 'admin') {
            return redirect('/verwaltung/login')
                ->with('status', 'Dein Admin-Konto wurde aktiviert. Du kannst dich jetzt hier anmelden.');
        }

        auth('member')->login($member);

        return redirect()->route('member.portal')->with('success', 'Willkommen! Dein Konto wurde aktiviert.');
    }
}
