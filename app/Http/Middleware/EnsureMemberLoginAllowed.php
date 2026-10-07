<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberLoginAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = $request->user('member');

        if ($member && ($member->is_login_blocked || $member->status !== 'approved')) {
            auth('member')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('member.login')
                ->withErrors(['email' => 'Dein Zugang ist derzeit gesperrt oder noch nicht freigeschaltet.']);
        }

        return $next($request);
    }
}
