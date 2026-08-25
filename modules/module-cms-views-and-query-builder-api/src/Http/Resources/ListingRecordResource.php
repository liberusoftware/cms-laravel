<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ListingRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'cms-listing-record',
            'attributes' => $this->resource->getAttributes(),
        ];
    }
}
