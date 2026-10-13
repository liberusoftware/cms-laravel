<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\AccountSetupWizard;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAccountSetup
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $user->setup_completed_at !== null || session('account_setup_required') !== true) {
            return $next($request);
        }

        if ($request->routeIs('filament.app.pages.account-setup-wizard') || $request->routeIs('filament.app.auth.logout')) {
            return $next($request);
        }

        return redirect()->to(AccountSetupWizard::getUrl());
    }
}
