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

final readonly class WebDeliveryController
{
    public function __construct(private DeliveryRouteQuery $routes, private WebDeliveryService $delivery) {}

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
        $data = $this->validated($request, ['path' => ['required', 'string', 'max:2048'], 'route_type' => ['nullable', 'in:content,redirect,error'], 'content_type' => ['nullable', 'string', 'max:255'], 'content_id' => ['nullable', 'string', 'max:255'], 'body' => ['nullable', 'string'], 'canonical_url' => ['nullable', 'url'], 'redirect_url' => ['nullable', 'url'], 'redirect_status' => ['nullable', 'integer'], 'metadata' => ['nullable', 'array'], 'cache_tags' => ['nullable', 'array'], 'status' => ['nullable', 'in:draft,published']]);

        return new DeliveryRouteResource($this->delivery->registerRoute($data));
    }

    public function update(Request $request, int $route): DeliveryRouteResource
    {
        $record = $this->routeById($route);
        $data = $this->validated($request, ['path' => ['sometimes', 'string', 'max:2048'], 'route_type' => ['sometimes', 'in:content,redirect,error'], 'content_type' => ['nullable', 'string', 'max:255'], 'content_id' => ['nullable', 'string', 'max:255'], 'body' => ['nullable', 'string'], 'canonical_url' => ['nullable', 'url'], 'redirect_url' => ['nullable', 'url'], 'redirect_status' => ['nullable', 'integer'], 'metadata' => ['nullable', 'array'], 'cache_tags' => ['nullable', 'array'], 'status' => ['nullable', 'in:draft,published'], 'error_status' => ['nullable', 'integer', 'between:400,599'], 'error_message' => ['nullable', 'string']]);

        return new DeliveryRouteResource($this->delivery->updateRoute($record, $data));
    }

    public function destroy(int $route): JsonResponse
    {
        $this->delivery->deleteRoute($this->routeById($route));

        return response()->json(status: 204);
    }

    public function resolve(Request $request): JsonResponse
    {
        $data = $this->validated($request, ['path' => ['required', 'string', 'max:2048'], 'preview_token' => ['nullable', 'string', 'max:255']]);
        $path = is_string($data['path'] ?? null) ? $data['path'] : '';
        $previewToken = is_string($data['preview_token'] ?? null) ? $data['preview_token'] : null;
        $result = $this->delivery->render($path, $previewToken);

        return response()->json(['data' => ['path' => $result->path, 'body' => $result->body, 'metadata' => $result->metadata, 'cache_tags' => $result->cacheTags, 'canonical_url' => $result->canonicalUrl, 'redirect_url' => $result->redirectUrl, 'preview' => $result->preview]], $result->status);
    }

    public function previewToken(int $route): JsonResponse
    {
        $record = DeliveryRoute::query()->find($route);
        if (! $record) {
            throw new NotFoundHttpException;
        }

        $fresh = $record->fresh();

        return response()->json(['token' => $this->delivery->issuePreviewToken($record), 'expires_at' => $fresh?->preview_expires_at?->toISOString()]);
    }

    public function invalidate(Request $request): JsonResponse
    {
        $data = $this->validated($request, ['cache_tags' => ['required', 'array', 'min:1'], 'cache_tags.*' => ['string', 'max:255'], 'idempotency_key' => ['required', 'string', 'max:255'], 'provider' => ['nullable', 'string', 'max:255']]);
        $tags = is_array($data['cache_tags'] ?? null) ? array_values(array_filter($data['cache_tags'], is_string(...))) : [];
        $key = is_string($data['idempotency_key'] ?? null) ? $data['idempotency_key'] : '';
        $provider = is_string($data['provider'] ?? null) ? $data['provider'] : null;
        $record = $this->delivery->invalidate($tags, $key, $provider);

        $recordId = $record->getKey();

        return response()->json(['data' => ['id' => is_scalar($recordId) ? (string) $recordId : '', 'type' => 'cms-delivery-invalidation', 'status' => $record->status, 'cache_tags' => $record->cache_tags]], 202);
    }

    public function maintenance(Request $request, int $route): DeliveryRouteResource
    {
        $record = $this->routeById($route);
        $data = $this->validated($request, ['enabled' => ['required', 'boolean']]);

        return new DeliveryRouteResource($this->delivery->setMaintenance($record, (bool) $data['enabled']));
    }

    private function routeById(int $route): DeliveryRoute
    {
        $record = DeliveryRoute::query()->find($route);
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return $record;
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $rules): array
    {
        $validated = $request->validate($rules);
        if (! is_array($validated)) {
            return [];
        }
        $result = [];
        foreach ($validated as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
