<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\PlatformDashboard;
use App\Filament\Admin\Pages\PlatformSetup;
use App\Filament\Pages\ManageGeneralSettings;
use App\Http\Middleware\EnsureTwoFactorForPrivilegedUsers;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->id('admin')->path('platform-admin')
            ->brandName('Liberu CMS · Administration')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->colors(['primary' => Color::Indigo])
            ->sidebarCollapsibleOnDesktop()
            ->login()->passwordReset()->profile()
            ->pages([PlatformDashboard::class, PlatformSetup::class, ManageGeneralSettings::class])
            ->middleware([
                EncryptCookies::class, AddQueuedCookiesToResponse::class,
                StartSession::class, AuthenticateSession::class,
                ShareErrorsFromSession::class, VerifyCsrfToken::class,
                SubstituteBindings::class, DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class, EnsureTwoFactorForPrivilegedUsers::class], isPersistent: true)
            ->plugins([TwoFactorAuthenticationPlugin::make()->enableTwoFactorAuthentication()]);
    }
}
