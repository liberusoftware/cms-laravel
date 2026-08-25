<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;

final class DeliveryRouteQuery
{
    public function paginate(int $perPage = 15, string $search = '', ?string $status = null): LengthAwarePaginator
    {
        $term = trim($search);

        return DeliveryRoute::query()
            ->when($term !== '', fn ($query) => $query->where('path', 'like', "%{$term}%"))
            ->when($status !== null && $status !== '', fn ($query) => $query->where('status', $status))
            ->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $path): ?DeliveryRoute
    {
        return DeliveryRoute::query()->where('path', '/'.trim($path, '/'))->first();
    }
}
