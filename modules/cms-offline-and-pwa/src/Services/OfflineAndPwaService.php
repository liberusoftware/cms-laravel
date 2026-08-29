<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwa\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;

final class OfflineAndPwaService
{
    public function configure(string $siteKey, string $name, string $shortName, ?int $teamId = null, array $attributes = []): PwaConfiguration
    {
        if (trim($siteKey) === '' || trim($name) === '') {
            throw ValidationException::withMessages(['site_key' => 'A site key and name are required.']);
        }
        if (mb_strlen($shortName) > 12) {
            throw ValidationException::withMessages(['short_name' => 'The short name must be 12 characters or fewer.']);
        }

        return PwaConfiguration::query()->updateOrCreate(
            ['site_key' => $siteKey, 'team_id' => $teamId],
            array_merge(['name' => $name, 'short_name' => $shortName, 'team_id' => $teamId], $this->validatedAttributes($attributes)),
        );
    }

    /** @param array<string, mixed> $attributes */
    public function update(PwaConfiguration $configuration, array $attributes): PwaConfiguration
    {
        $siteKey = (string) ($attributes['site_key'] ?? $configuration->site_key);
        $name = (string) ($attributes['name'] ?? $configuration->name);
        $shortName = (string) ($attributes['short_name'] ?? $configuration->short_name);
        $validated = $this->validatedAttributes($attributes);

        if (mb_strlen($shortName) > 12 || trim($siteKey) === '' || trim($name) === '') {
            throw ValidationException::withMessages(['configuration' => 'The PWA configuration metadata is invalid.']);
        }

        $configuration->update(array_merge($validated, ['site_key' => $siteKey, 'name' => $name, 'short_name' => $shortName]));

        return $configuration->refresh();
    }

    public function setCachePolicy(PwaConfiguration $configuration, array $policy): PwaConfiguration
    {
        $defaults = ['precache' => [], 'runtime' => [], 'max_entries' => 100, 'max_age_seconds' => 86400, 'offline_fallback' => true];
        $policy = array_replace($defaults, $policy);
        if (! is_array($policy['precache']) || ! is_array($policy['runtime']) || ! is_int($policy['max_entries']) || $policy['max_entries'] < 1 || ! is_int($policy['max_age_seconds']) || $policy['max_age_seconds'] < 1) {
            throw ValidationException::withMessages(['cache_policy' => 'Cache policy has invalid entries or limits.']);
        }
        $configuration->update(['cache_policy' => $policy, 'last_updated_at' => now()]);

        return $configuration->refresh();
    }

    public function publishUpdate(PwaConfiguration $configuration, ?string $version = null): PwaConfiguration
    {
        $configuration->update(['service_worker_version' => $version ?: (string) Str::ulid(), 'last_updated_at' => now()]);

        return $configuration->refresh();
    }

    /** @return array<string, mixed> */
    public function manifest(PwaConfiguration $configuration): array
    {
        return ['name' => $configuration->name, 'short_name' => $configuration->short_name, 'start_url' => $configuration->start_url, 'scope' => $configuration->scope, 'display' => $configuration->display, 'theme_color' => $configuration->theme_color, 'background_color' => $configuration->background_color, 'icons' => $configuration->icon_url ? [['src' => $configuration->icon_url, 'sizes' => '512x512', 'type' => 'image/png']] : []];
    }

    public function serviceWorker(PwaConfiguration $configuration): string
    {
        $cacheName = 'cms-pwa-'.$configuration->service_worker_version;
        $precache = json_encode($configuration->cache_policy['precache'] ?? [], JSON_THROW_ON_ERROR);
        $fallback = json_encode($configuration->offline_url ?: '/offline', JSON_THROW_ON_ERROR);

        return 'const CACHE_NAME = '.json_encode($cacheName, JSON_THROW_ON_ERROR).";\nconst PRECACHE = {$precache};\nself.addEventListener('install', event => event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE)).then(() => self.skipWaiting())));\nself.addEventListener('activate', event => event.waitUntil(self.clients.claim()));\nself.addEventListener('fetch', event => { if (event.request.method !== 'GET') return; event.respondWith(fetch(event.request).catch(() => caches.match(event.request).then(response => response || caches.match({$fallback})))); });";
    }

    /** @return array<string, mixed> */
    private function validatedAttributes(array $attributes): array
    {
        $allowed = ['start_url', 'scope', 'display', 'theme_color', 'background_color', 'icon_url', 'offline_url', 'cache_policy', 'service_worker_version'];
        $result = array_intersect_key($attributes, array_flip($allowed));
        foreach (['start_url', 'scope', 'offline_url'] as $key) {
            if (isset($result[$key]) && ! $this->isSafePath($result[$key])) {
                throw ValidationException::withMessages([$key => 'PWA paths must be site-relative.']);
            }
        }

        return $result;
    }

    private function isSafePath(mixed $path): bool
    {
        if (! is_string($path) || ! str_starts_with($path, '/') || preg_match('/[\x00-\x20]/', $path) || str_contains($path, '\\')) {
            return false;
        }
        $pathOnly = explode('?', $path, 2)[0];

        return ! in_array('..', explode('/', $pathOnly), true);
    }
}
