<?php

namespace App\Providers\Filament;

use App\Filament\Organisation\Pages\Dashboard;
use App\Filament\Organisation\Pages\Members;
use App\Filament\Organisation\Pages\Profile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganisationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('organisation')
            ->path('verwaltung/organisation')
            ->authGuard('organisation')
            ->login(fn () => redirect()->route('member.login'))
            ->brandName('Organisationsbereich')
            ->brandLogo(asset('img/fwz-logo-new2.svg'))
            ->brandLogoHeight('2.5rem')
            ->colors(['primary' => Color::Amber])
            ->pages([Dashboard::class, Members::class, Profile::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
