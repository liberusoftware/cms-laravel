<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\WebDelivery\Actions\WebDeliveryService;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;
use Liberu\Cms\WebDelivery\Queries\DeliveryRouteQuery;
use Liberu\Cms\WebDeliveryApi\Http\Resources\DeliveryRouteResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WebDeliveryController
{
    public function __construct(private readonly DeliveryRouteQuery $routes, private readonly WebDeliveryService $delivery) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return DeliveryRouteResource::collection($this->routes->paginate($request->integer('per_page', 15), (string) $request->string('search'), $request->string('status')->toString() ?: null));
    }

    public function show(string $path): DeliveryRouteResource
    {
        $route = $this->routes->find($path);
        if (! $route) {
            throw new NotFoundHttpException;
        }

        return new DeliveryRouteResource($route);
    }

    public function create(Request $request): DeliveryRouteResource
    {
        $data = $request->validate(['path' => ['required', 'string', 'max:2048'], 'route_type' => ['nullable', 'in:content,redirect,error'], 'content_type' => ['nullable', 'string', 'max:255'], 'content_id' => ['nullable', 'string', 'max:255'], 'body' => ['nullable', 'string'], 'canonical_url' => ['nullable', 'url'], 'redirect_url' => ['nullable', 'url'], 'redirect_status' => ['nullable', 'integer'], 'metadata' => ['nullable', 'array'], 'cache_tags' => ['nullable', 'array'], 'status' => ['nullable', 'in:draft,published']]);

        return new DeliveryRouteResource($this->delivery->registerRoute($data));
    }

    public function resolve(Request $request): JsonResponse
    {
        $data = $request->validate(['path' => ['required', 'string', 'max:2048'], 'preview_token' => ['nullable', 'string', 'max:255']]);
        $result = $this->delivery->render($data['path'], $data['preview_token'] ?? null);

        return response()->json(['data' => ['path' => $result->path, 'body' => $result->body, 'metadata' => $result->metadata, 'cache_tags' => $result->cacheTags, 'canonical_url' => $result->canonicalUrl, 'redirect_url' => $result->redirectUrl, 'preview' => $result->preview]], $result->status);
    }

    public function previewToken(int $route): JsonResponse
    {
        $record = DeliveryRoute::query()->find($route);
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return response()->json(['token' => $this->delivery->issuePreviewToken($record), 'expires_at' => $record->fresh()->preview_expires_at?->toISOString()]);
    }

    public function invalidate(Request $request): JsonResponse
    {
        $data = $request->validate(['cache_tags' => ['required', 'array', 'min:1'], 'cache_tags.*' => ['string', 'max:255'], 'idempotency_key' => ['required', 'string', 'max:255'], 'provider' => ['nullable', 'string', 'max:255']]);
        $record = $this->delivery->invalidate($data['cache_tags'], $data['idempotency_key'], $data['provider'] ?? null);

        return response()->json(['data' => ['id' => (string) $record->getKey(), 'type' => 'cms-delivery-invalidation', 'status' => $record->status, 'cache_tags' => $record->cache_tags]], 202);
    }

    public function maintenance(Request $request, int $route): DeliveryRouteResource
    {
        $record = DeliveryRoute::query()->find($route);
        if (! $record) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['enabled' => ['required', 'boolean']]);

        return new DeliveryRouteResource($this->delivery->setMaintenance($record, (bool) $data['enabled']));
    }
}
