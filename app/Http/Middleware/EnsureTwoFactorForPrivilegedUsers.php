<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires privileged users to enrol in two-factor authentication before they
 * can use the admin panel (OWASP A07). A signed-in user holding a privileged
 * role who has not enabled 2FA is redirected to the setup page; non-privileged
 * users are unaffected. Runs in the panel's auth middleware, so the user is
 * always resolved by the time it executes.
 */
class EnsureTwoFactorForPrivilegedUsers
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('two-factor.enforce')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user instanceof User || ! $this->isPrivileged($user) || $user->hasEnabledTwoFactorAuthentication()) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        if ($panel === null) {
            return $next($request);
        }

        // Let the 2FA and logout routes through so the redirect can't loop.
        $panelId = $panel->getId();

        if ($request->routeIs("filament.{$panelId}.two-factor.*") || $request->routeIs("filament.{$panelId}.auth.logout")) {
            return $next($request);
        }

        return redirect()->to($panel->route('two-factor.setup'));
    }

    private function isPrivileged(User $user): bool
    {
        // Resolve roles in the user's own team so the check is deterministic
        // regardless of when the panel tenant is bound during the request.
        if (config('permission.teams')) {
            $teamId = $user->getAttribute('current_team_id');

            if (is_numeric($teamId)) {
                setPermissionsTeamId((int) $teamId);
            }
        }

        $roles = config('two-factor.privileged_roles', []);

        return is_array($roles) && $roles !== [] && $user->hasAnyRole($roles);
    }
}
