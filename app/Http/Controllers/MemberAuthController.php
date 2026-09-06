<?php

namespace App\Http\Controllers;

use App\Mail\MemberPasswordResetCodeMail;
use App\Models\LoginLog;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MemberAuthController extends Controller
{
    private const RESET_CODE_LIFETIME_MINUTES = 10;

    private const RESET_CODE_MAX_ATTEMPTS = 5;

    public function showLogin()
    {
        if (auth('member')->check()) {
            return redirect()->route('member.portal');
        }

        return view('auth.member-login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
                'password.required' => 'Bitte gib dein Passwort ein.',
            ]);
        } catch (ValidationException $exception) {
            LoginLog::record(
                null,
                trim((string) $request->input('email')) ?: null,
                false,
                'Ungültige Eingaben',
            );

            throw $exception;
        }

        if (auth('member')->attempt($credentials, $request->boolean('remember'))) {
            $member = auth('member')->user();
            if ($member->status !== 'approved') {
                LoginLog::record(
                    $member,
                    $credentials['email'],
                    false,
                    'Konto noch nicht freigeschaltet.',
                );
                auth('member')->logout();

                return back()->withErrors(['email' => 'Dein Konto wurde noch nicht freigeschaltet.']);
            }
            LoginLog::record($member, $credentials['email'], true);
            $request->session()->regenerate();

            return redirect()->route('member.portal');
        }

        LoginLog::record(
            Member::where('email', $credentials['email'])->first(),
            $credentials['email'],
            false,
            'E-Mail oder Passwort ist falsch.',
        );

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

    public function sendResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Bitte gib deine E-Mail-Adresse ein.',
            'email.email' => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        ]);

        $email = strtolower(trim($validated['email']));
        $member = Member::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($member) {
            $code = (string) random_int(100000, 999999);

            DB::table('member_password_reset_codes')->updateOrInsert([
                'email' => $email,
            ], [
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::RESET_CODE_LIFETIME_MINUTES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Mail::to($member->email)->send(new MemberPasswordResetCodeMail(
                $code,
                self::RESET_CODE_LIFETIME_MINUTES,
            ));
        }

        $request->session()->forget('member_password_reset');
        $request->session()->put('member_password_reset_email', $email);

        return redirect()
            ->route('member.reset.code')
            ->with('status', 'Falls ein Konto mit dieser E-Mail-Adresse existiert, wurde ein sechsstelliger Code gesendet.');
    }

    public function showResetCode(Request $request)
    {
        if (! $request->session()->has('member_password_reset_email')) {
            return redirect()->route('member.forgot');
        }

        return view('auth.member-reset-code');
    }

    public function verifyResetCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Bitte gib den sechsstelligen Code ein.',
            'code.digits' => 'Der Code muss aus genau sechs Ziffern bestehen.',
        ]);

        $email = $request->session()->get('member_password_reset_email');
        if (! is_string($email)) {
            return redirect()->route('member.forgot');
        }

        $reset = DB::table('member_password_reset_codes')->where('email', $email)->first();

        if (! $reset || now()->greaterThan($reset->expires_at)) {
            DB::table('member_password_reset_codes')->where('email', $email)->delete();

            return back()->withErrors([
                'code' => 'Der Code ist abgelaufen. Bitte fordere einen neuen Code an.',
            ]);
        }

        if ($reset->attempts >= self::RESET_CODE_MAX_ATTEMPTS) {
            DB::table('member_password_reset_codes')->where('email', $email)->delete();

            return back()->withErrors([
                'code' => 'Zu viele Fehlversuche. Bitte fordere einen neuen Code an.',
            ]);
        }

        if (! Hash::check($validated['code'], $reset->code_hash)) {
            DB::table('member_password_reset_codes')
                ->where('email', $email)
                ->increment('attempts');

            return back()->withErrors([
                'code' => 'Der eingegebene Code ist nicht korrekt.',
            ]);
        }

        $request->session()->put('member_password_reset', [
            'email' => $email,
            'verified_at' => now()->timestamp,
        ]);

        return redirect()->route('member.reset.password');
    }

    public function showResetPassword(Request $request)
    {
        if (! $this->hasValidResetSession($request)) {
            return redirect()->route('member.forgot');
        }

        return view('auth.member-reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (! $this->hasValidResetSession($request)) {
            return redirect()
                ->route('member.forgot')
                ->withErrors(['email' => 'Die Passwort-Zurücksetzung ist abgelaufen. Bitte beginne erneut.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Bitte wähle ein neues Passwort.',
            'password.min' => 'Das Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein.',
        ]);

        $resetSession = $request->session()->get('member_password_reset');
        $email = $resetSession['email'];
        $reset = DB::table('member_password_reset_codes')->where('email', $email)->first();

        if (! $reset || now()->greaterThan($reset->expires_at)) {
            $request->session()->forget(['member_password_reset', 'member_password_reset_email']);

            return redirect()
                ->route('member.forgot')
                ->withErrors(['email' => 'Die Passwort-Zurücksetzung ist abgelaufen. Bitte beginne erneut.']);
        }

        $member = Member::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if (! $member) {
            $request->session()->forget(['member_password_reset', 'member_password_reset_email']);

            return redirect()->route('member.login');
        }

        $member->update(['password' => $validated['password']]);
        DB::table('member_password_reset_codes')->where('email', $email)->delete();
        $request->session()->forget(['member_password_reset', 'member_password_reset_email']);

        return redirect()
            ->route('member.login')
            ->with('status', 'Dein Passwort wurde geändert. Du kannst dich jetzt anmelden.');
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

    private function hasValidResetSession(Request $request): bool
    {
        $resetSession = $request->session()->get('member_password_reset');

        if (! is_array($resetSession)
            || ! isset($resetSession['email'], $resetSession['verified_at'])
            || ! is_string($resetSession['email'])
            || ! is_numeric($resetSession['verified_at'])) {
            return false;
        }

        return now()->timestamp - (int) $resetSession['verified_at']
            <= self::RESET_CODE_LIFETIME_MINUTES * 60;
    }
}
