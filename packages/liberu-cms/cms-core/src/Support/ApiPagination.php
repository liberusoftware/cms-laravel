<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Turns a repository's array of live models into a page-based paginator with the
 * standard `data`/`meta`/`links` shape, honouring the consumer's `per_page` up
 * to the API's hard cap. Centralised here so every Delivery API collection
 * endpoint paginates identically without depending on the API package.
 *
 * @internal Used by content modules' Delivery API controllers.
 */
final class ApiPagination
{
    /**
     * @param  array<int, mixed>  $items
     * @return LengthAwarePaginator<int, mixed>
     */
    public static function fromArray(array $items): LengthAwarePaginator
    {
        $defaultRaw = config('cms-api.pagination.default', 15);
        $maxRaw = config('cms-api.pagination.max', 100);

        $default = is_numeric($defaultRaw) ? (int) $defaultRaw : 15;
        $max = is_numeric($maxRaw) ? (int) $maxRaw : 100;

        $requested = request()->has('per_page')
            ? request()->integer('per_page')
            : $default;

        $perPage = max(1, min($requested, $max));

        $collection = Collection::make($items);
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ],
        );
    }
}
