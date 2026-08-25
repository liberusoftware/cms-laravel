<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingApi\Http\Resources;

use Liberu\Cms\StaticPublishing\Models\StaticBuild;

final class StaticBuildResource
{
    /** @return array<string, mixed> */
    public static function make(StaticBuild $build): array { return ['id' => (string) $build->getKey(), 'type' => 'cms-static-build', 'site_key' => $build->site_key, 'state' => $build->state, 'kind' => $build->kind, 'deployment' => $build->deployment, 'manifest' => $build->manifest ?? [], 'diagnostics' => $build->diagnostics ?? [], 'parent_build_id' => $build->parent_build_id, 'checksum' => $build->checksum, 'started_at' => $build->started_at?->toISOString(), 'finished_at' => $build->finished_at?->toISOString()]; }
}
