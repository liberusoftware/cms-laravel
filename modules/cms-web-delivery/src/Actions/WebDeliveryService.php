<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\WebDelivery\Events\DeliveryCacheInvalidated;
use Liberu\Cms\WebDelivery\Events\DeliveryRouteRegistered;
use Liberu\Cms\WebDelivery\Models\DeliveryInvalidation;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;
use Liberu\Cms\WebDelivery\Support\DeliveryResult;
use Liberu\Cms\WebDelivery\Support\EdgeInvalidationRegistry;

final readonly class WebDeliveryService
{
    public function __construct(private EdgeInvalidationRegistry $edge) {}

    /** @param array<string, mixed> $attributes */
    public function registerRoute(array $attributes): DeliveryRoute
    {
        $path = $this->path($attributes['path'] ?? '');
        $type = $attributes['route_type'] ?? 'content';
        if (! is_string($type) || ! in_array($type, ['content', 'redirect', 'error'], true)) {
            throw ValidationException::withMessages(['route_type' => 'Unsupported delivery route type.']);
        }
        $redirectStatus = $this->integer($attributes['redirect_status'] ?? null);
        if ($type === 'redirect' && (! is_string($attributes['redirect_url'] ?? null) || ! in_array($redirectStatus, $this->allowedRedirectStatuses(), true))) {
            throw ValidationException::withMessages(['redirect_url' => 'Redirect routes require a URL and supported status.']);
        }
        if ($type === 'error' && ! isset($attributes['error_status'])) {
            throw ValidationException::withMessages(['error_status' => 'Error routes require an HTTP status.']);
        }

        $route = DB::transaction(fn (): DeliveryRoute => DeliveryRoute::query()->updateOrCreate(
            ['path' => $path, 'team_id' => $attributes['team_id'] ?? null],
            [...$attributes, 'path' => $path, 'route_type' => $type, 'cache_ttl' => $attributes['cache_ttl'] ?? config('web-delivery.default_cache_ttl', 300), 'cache_tags' => $this->tags($attributes['cache_tags'] ?? null, $path)],
        ));
        event(new DeliveryRouteRegistered($route));

        return $route;
    }

    /** @param array<string, mixed> $attributes */
    public function updateRoute(DeliveryRoute $route, array $attributes): DeliveryRoute
    {
        $path = array_key_exists('path', $attributes) ? $this->path($attributes['path']) : $route->path;
        $type = $attributes['route_type'] ?? $route->route_type;
        if (! is_string($type) || ! in_array($type, ['content', 'redirect', 'error'], true)) {
            throw ValidationException::withMessages(['route_type' => 'Unsupported delivery route type.']);
        }

        $redirectStatus = $this->integer($attributes['redirect_status'] ?? $route->redirect_status);
        if ($type === 'redirect' && (! is_string($attributes['redirect_url'] ?? $route->redirect_url) || ! in_array($redirectStatus, $this->allowedRedirectStatuses(), true))) {
            throw ValidationException::withMessages(['redirect_url' => 'Redirect routes require a URL and supported status.']);
        }
        if ($type === 'error' && ! array_key_exists('error_status', $attributes) && $route->error_status === null) {
            throw ValidationException::withMessages(['error_status' => 'Error routes require an HTTP status.']);
        }

        $allowed = array_intersect_key($attributes, array_flip([
            'path', 'route_type', 'content_type', 'content_id', 'body', 'canonical_url', 'redirect_url',
            'redirect_status', 'metadata', 'cache_tags', 'cache_ttl', 'status', 'error_status', 'error_message',
        ]));
        $route->fill([...$allowed, 'path' => $path, 'route_type' => $type, 'cache_tags' => $this->tags($attributes['cache_tags'] ?? $route->cache_tags, $path)]);
        $route->save();

        return $route->refresh();
    }

    public function deleteRoute(DeliveryRoute $route): void
    {
        DB::transaction(fn (): ?bool => $route->delete());
    }

    public function render(string $path, ?string $previewToken = null): DeliveryResult
    {
        $normalized = $this->path($path);
        $route = DeliveryRoute::query()->where('path', $normalized)->first();
        if (! $route) {
            return new DeliveryResult(404, $normalized, null, [], [], null, null, false);
        }
        $preview = $this->validPreview($route, $previewToken);
        if ($route->maintenance) {
            return new DeliveryResult($this->integer(config('web-delivery.maintenance_status', 503)) ?? 503, $normalized, null, $route->metadata ?? [], $route->cache_tags ?? [], $route->canonical_url, null, $preview);
        }
        if ($route->status !== 'published' && ! $preview) {
            return new DeliveryResult(404, $normalized, null, [], [], null, null, false);
        }
        if ($route->route_type === 'redirect') {
            return new DeliveryResult($this->integer($route->redirect_status) ?? 302, $normalized, null, $route->metadata ?? [], $route->cache_tags ?? [], $route->canonical_url, $route->redirect_url, $preview);
        }
        if ($route->route_type === 'error') {
            return new DeliveryResult((int) ($route->error_status ?? 500), $normalized, $route->error_message, $route->metadata ?? [], $route->cache_tags ?? [], $route->canonical_url, null, $preview);
        }

        return new DeliveryResult(200, $normalized, $route->body, $route->metadata ?? [], $route->cache_tags ?? [], $route->canonical_url, null, $preview);
    }

    public function issuePreviewToken(DeliveryRoute $route): string
    {
        $token = Str::random(48);
        $route->update(['preview_enabled' => true, 'preview_token_hash' => hash('sha256', $token), 'preview_expires_at' => now()->addSeconds($this->integer(config('web-delivery.preview_ttl', 900)) ?? 900)]);

        return $token;
    }

    public function setMaintenance(DeliveryRoute $route, bool $enabled): DeliveryRoute
    {
        $route->update(['maintenance' => $enabled]);

        return $route->refresh();
    }

    /** @param array<int, string> $cacheTags */
    public function invalidate(array $cacheTags, string $idempotencyKey, ?string $provider = null, int|string|null $teamId = null): DeliveryInvalidation
    {
        $tags = $this->tags($cacheTags, '');
        if ($tags === [] || trim($idempotencyKey) === '') {
            throw ValidationException::withMessages(['cache_tags' => 'At least one cache tag and an idempotency key are required.']);
        }
        $invalidation = DeliveryInvalidation::query()->firstOrCreate(['idempotency_key' => $idempotencyKey, 'team_id' => $teamId], ['cache_tags' => $tags, 'provider' => $provider, 'status' => 'pending']);
        if ($invalidation->status === 'completed') {
            return $invalidation;
        }
        try {
            $this->edge->invalidate($invalidation);
            $invalidation->update(['status' => 'completed', 'completed_at' => now(), 'provider' => $provider]);
        } catch (\Throwable $exception) {
            $invalidation->update(['status' => 'failed', 'failure_reason' => Str::limit($exception->getMessage(), 1000)]);
            throw $exception;
        }
        $result = $invalidation->refresh();
        event(new DeliveryCacheInvalidated($result));

        return $result;
    }

    private function validPreview(DeliveryRoute $route, ?string $token): bool
    {
        return $route->preview_enabled && is_string($token) && $token !== '' && $route->preview_expires_at?->isFuture() && hash_equals((string) $route->preview_token_hash, hash('sha256', $token));
    }

    private function path(mixed $path): string
    {
        if (! is_string($path) || trim($path) === '' || str_contains($path, '..')) {
            throw ValidationException::withMessages(['path' => 'A safe non-empty delivery path is required.']);
        }

        return '/'.trim(parse_url($path, PHP_URL_PATH) ?: $path, '/');
    }

    /** @return array<int, int> */
    private function allowedRedirectStatuses(): array
    {
        $statuses = config('web-delivery.allowed_redirect_statuses', []);

        return is_array($statuses) ? array_values(array_filter($statuses, is_int(...))) : [];
    }

    private function integer(mixed $value): ?int
    {
        return is_int($value) ? $value : (is_string($value) && ctype_digit($value) ? (int) $value : null);
    }

    /** @return array<int, string> */
    private function tags(mixed $tags, string $path): array
    {
        $values = is_array($tags) ? $tags : [];
        $result = [];
        foreach ($values as $tag) {
            if (is_string($tag) && trim($tag) !== '') {
                $result[] = $tag;
            }
        }

        return array_values(array_unique($result ?: ($path !== '' ? ["path:{$path}"] : [])));
    }
}
