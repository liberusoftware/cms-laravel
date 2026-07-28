<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Http\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records request count and latency for the Delivery API. Owned by
 * cms-observability and self-appended to the global stack, it self-filters to
 * `api/v1/*` so cms-api never references it and no other path is measured. The
 * recorder is resolved under a `bound()` guard, so the middleware stays inert if
 * the metrics seam is unbound.
 */
final readonly class RecordApiMetrics
{
    public function __construct(private Container $container) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('api/v1/*')) {
            return $next($request);
        }

        $start = microtime(true);
        $response = $next($request);
        $milliseconds = (microtime(true) - $start) * 1000;

        $this->record($request, $response, $milliseconds);

        return $response;
    }

    private function record(Request $request, Response $response, float $milliseconds): void
    {
        if (! $this->container->bound(MetricsRecorderInterface::class)) {
            return;
        }

        /** @var MetricsRecorderInterface $recorder */
        $recorder = $this->container->make(MetricsRecorderInterface::class);

        $route = $request->route();
        $routeName = $route instanceof Route ? $route->getName() : null;

        $tags = [
            'route' => $routeName ?? $request->path(),
            'status' => $response->getStatusCode(),
        ];

        $recorder->increment('api.request', tags: $tags);
        $recorder->timing('api.request', $milliseconds, $tags);
    }
}
