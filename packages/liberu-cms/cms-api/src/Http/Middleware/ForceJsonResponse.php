<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces every Delivery API request to be treated as a JSON request, so
 * authentication and validation failures are rendered as JSON status responses
 * (e.g. a clean 401) rather than redirected to a web login route. The API has no
 * HTML surface, so this holds for browser `fetch` calls that omit an Accept
 * header just as it does for explicit JSON clients.
 */
final class ForceJsonResponse
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
