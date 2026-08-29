<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ExtensionMarketplace\Queries\ExtensionMarketplaceQuery;
use Liberu\Cms\ExtensionMarketplace\Services\ExtensionMarketplaceService;
use Liberu\Cms\ExtensionMarketplaceApi\Http\Resources\ExtensionListingResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExtensionMarketplaceController
{
    public function index(Request $request, ExtensionMarketplaceQuery $query): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $listings = $query->catalog((int) ($data['per_page'] ?? 15), (string) ($data['search'] ?? ''));

        return response()->json(['data' => ExtensionListingResource::collection($listings->getCollection()), 'meta' => ['current_page' => $listings->currentPage(), 'last_page' => $listings->lastPage(), 'per_page' => $listings->perPage(), 'total' => $listings->total()]]);
    }

    public function show(string $key, ExtensionMarketplaceQuery $query): ExtensionListingResource
    {
        $listing = $query->find($key);
        if (! $listing || $listing->status !== 'published' || $listing->security_status !== 'approved') {
            throw new NotFoundHttpException;
        }

        return new ExtensionListingResource($listing);
    }

    public function store(Request $request, ExtensionMarketplaceService $service): ExtensionListingResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'publisher_id' => ['required', 'integer'], 'category_id' => ['nullable', 'integer'], 'description' => ['nullable', 'string'], 'license' => ['nullable', 'string', 'max:100'], 'metadata' => ['sometimes', 'array']]);

        return new ExtensionListingResource($service->listing($data, $request->user()?->current_team_id));
    }

    public function security(Request $request, string $key, ExtensionMarketplaceQuery $query, ExtensionMarketplaceService $service): ExtensionListingResource
    {
        $listing = $query->find($key);
        if (! $listing) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['security_status' => ['required', 'in:pending,approved,rejected']]);

        return new ExtensionListingResource($service->security($listing, $data['security_status']));
    }

    public function publish(string $key, ExtensionMarketplaceQuery $query, ExtensionMarketplaceService $service): ExtensionListingResource
    {
        $listing = $query->find($key);
        if (! $listing) {
            throw new NotFoundHttpException;
        }

        return new ExtensionListingResource($service->publish($listing));
    }
}
