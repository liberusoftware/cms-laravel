<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ExtensionMarketplace\Services\ExtensionMarketplaceService;

final class ExtensionListingResource extends JsonResource
{
    public function toArray($request): array
    {
        $listing = $this->resource;

        return ['id' => (string) $listing->getKey(), 'type' => 'cms-extension-marketplace', 'key' => $listing->key, 'name' => $listing->name, 'description' => $listing->description, 'publisher' => $listing->publisher?->only(['key', 'name']), 'category' => $listing->category?->only(['key', 'name']), 'license' => $listing->license, 'status' => $listing->status, 'security_status' => $listing->security_status, 'versions' => $listing->versions?->map(fn ($version): array => ['id' => (string) $version->id, 'version' => $version->version, 'checksum' => $version->checksum, 'status' => $version->status, 'released_at' => $version->released_at?->toISOString()])->values(), 'rating' => app(ExtensionMarketplaceService::class)->ratingSummary($listing), 'created_at' => $listing->created_at?->toISOString(), 'updated_at' => $listing->updated_at?->toISOString()];
    }
}
