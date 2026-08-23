<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Http\Concerns;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Hooks\Filters\ApiResourceFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;

/**
 * Routes a Delivery API Resource's payload through the {@see ApiResourceFilter}
 * hook point, so an extension can reshape the response without replacing the
 * Resource. Kept in one place so every content Resource that uses it shares the
 * same seam; a Resource must opt in by using this trait and wrapping its
 * `toArray()` return in {@see withApiResourceFilter()}.
 *
 * @mixin JsonResource
 */
trait FiltersApiResource
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function withApiResourceFilter(array $payload): array
    {
        return app(HookBusInterface::class)
            ->apply(new ApiResourceFilter($payload, $this->resource))
            ->data;
    }
}
