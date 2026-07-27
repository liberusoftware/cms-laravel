<?php

namespace App\Http\Middleware;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Utils::isTenancyEnabled() && ($team = Filament::getTenant())) {
            setPermissionsTeamId($team->id);
        }

        return $next($request);
    }
}
