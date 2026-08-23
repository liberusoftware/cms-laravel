<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the request tenant context from the authenticated principal. Because the
 * Delivery token's tokenable is a Team, `auth:sanctum` has already resolved the
 * Team onto the request; naming it in the tenant context makes the tenancy
 * resolver — and therefore the tenant global scope — filter every query in this
 * request to that one tenant.
 */
final readonly class SetApiTenant
{
    public function __construct(private TenantContextInterface $context) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $team = $request->user();

        if ($team instanceof Model) {
            $key = $team->getKey();
            $this->context->setTenantId(is_int($key) || is_string($key) ? $key : null);
        }

        return $next($request);
    }
}
