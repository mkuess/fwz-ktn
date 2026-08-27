<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationEnabled
{
    public function handle(Request $request, Closure $next, string $settingKey): Response
    {
        abort_unless(Setting::enabled($settingKey), 404);

        return $next($request);
    }
}
