<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\StaticPublishing\Models\StaticBuild;
use Liberu\Cms\StaticPublishing\Models\StaticInvalidation;
use Liberu\Cms\StaticPublishing\Support\DeploymentAdapterRegistry;

final readonly class StaticPublishingService
{
    public function __construct(private DeploymentAdapterRegistry $deployments) {}

    /** @param array<int, array{path:string,url?:string,last_modified?:string}> $routes */
    public function build(array $routes, ?string $siteKey = null, string $kind = 'full', string $deployment = 'local', ?StaticBuild $parent = null): StaticBuild
    {
        if (! in_array($kind, ['full', 'incremental', 'preview'], true)) {
            throw ValidationException::withMessages(['kind' => 'Unsupported build kind.']);
        } $manifest = collect($routes)->filter(fn (array $route): bool => isset($route['path']) && str_starts_with($route['path'], '/'))->values()->all();
        $build = StaticBuild::query()->create(['site_key' => $siteKey, 'state' => 'building', 'kind' => $kind, 'deployment' => $deployment, 'manifest' => $manifest, 'parent_build_id' => $parent?->getKey(), 'started_at' => now()]);
        $build->forceFill(['state' => 'published', 'checksum' => hash('sha256', json_encode($manifest, JSON_THROW_ON_ERROR)), 'diagnostics' => ['route_count' => count($manifest), 'invalid_routes' => count($routes) - count($manifest)], 'finished_at' => now()])->save();

        return $build->fresh();
    }

    public function invalidate(StaticBuild $build, string $path, string $reason = 'content-changed'): StaticInvalidation
    {
        if (! str_starts_with($path, '/') || str_contains($path, '..') || str_contains($path, '\\')) {
            throw ValidationException::withMessages(['path' => 'Invalidation paths must start with /.']);
        } Cache::tags(['cms-static-publishing', 'cms-static-site-'.$build->site_key])->flush();

        return StaticInvalidation::query()->create(['build_id' => $build->getKey(), 'path' => $path, 'reason' => $reason, 'created_at' => now()]);
    }

    public function rollback(StaticBuild $build): StaticBuild
    {
        $parent = $build->parent_build_id ? StaticBuild::query()->findOrFail($build->parent_build_id) : null;
        if (! $parent) {
            throw ValidationException::withMessages(['build' => 'No previous build is available for rollback.']);
        } $build->forceFill(['state' => 'rolled_back', 'diagnostics' => ['rollback_to' => $parent->getKey()]])->save();

        return $build->fresh();
    }

    /** @return array{state:string,route_count:int,diagnostics:array<string,mixed>} */
    public function diagnostics(StaticBuild $build): array
    {
        return ['state' => $build->state, 'route_count' => count($build->manifest ?? []), 'diagnostics' => $build->diagnostics ?? []];
    }

    /** @return array<string, mixed> */
    public function deploy(StaticBuild $build, string $adapter): array
    {
        if ($build->state !== 'published') {
            throw ValidationException::withMessages(['build' => 'Only published builds can be deployed.']);
        }
        $result = $this->deployments->resolve($adapter)->deploy($build);
        $build->forceFill(['deployment' => $adapter, 'diagnostics' => [...($build->diagnostics ?? []), 'deployment' => $result]])->save();

        return $result;
    }
}
