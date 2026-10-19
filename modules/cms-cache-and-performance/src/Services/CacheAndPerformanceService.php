<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformance\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\CacheAndPerformance\Models\CacheEntry;
use Liberu\Cms\CacheAndPerformance\Models\CacheInvalidation;

final class CacheAndPerformanceService
{
    /**
     * @param  array<int, string>  $tags
     * @param  array<string, mixed>  $metadata
     * @return array{entry:CacheEntry,hit:bool}
     */
    public function remember(?int $teamId, string $key, string $type, int $ttl, callable $resolver, array $tags = [], array $metadata = []): array
    {
        $this->validateKey($key);
        if (! in_array($type, ['page', 'render', 'query', 'object'], true)) {
            throw ValidationException::withMessages(['cache_type' => 'Cache type must be page, render, query, or object.']);
        }
        if ($ttl < 1 || $ttl > 31536000) {
            throw ValidationException::withMessages(['ttl_seconds' => 'TTL must be between 1 second and 1 year.']);
        }
        $entry = CacheEntry::query()->firstOrCreate(['team_id' => $teamId, 'cache_key' => $key], ['cache_type' => $type, 'tags' => array_values(array_unique($tags)), 'status' => 'cold', 'ttl_seconds' => $ttl, 'metadata' => $metadata]);
        $hit = Cache::get($key) !== null;
        if ($hit) {
            $entry->increment('hits');

            $fresh = $entry->fresh();
            if (! $fresh) {
                throw new \RuntimeException('The cache entry could not be refreshed.');
            }

            return ['entry' => $fresh, 'hit' => true];
        }
        $value = $resolver();
        Cache::put($key, $value, $ttl);
        $entry->update(['status' => 'warm', 'tags' => array_values(array_unique($tags)), 'ttl_seconds' => $ttl, 'size_bytes' => strlen(serialize($value)), 'warmed_at' => now(), 'metadata' => $metadata]);
        $entry->increment('misses');

        $fresh = $entry->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The cache entry could not be refreshed.');
        }

        return ['entry' => $fresh, 'hit' => false];
    }

    /**
     * @param  array<int, string>  $tags
     * @param  array<int, string>  $keys
     */
    public function invalidate(?int $teamId, array $tags = [], array $keys = [], string $idempotencyKey = ''): CacheInvalidation
    {
        $tags = array_values(array_filter(array_unique($tags), static fn (string $tag): bool => trim($tag) !== ''));
        $keys = array_values(array_filter(array_unique($keys), static fn (mixed $key): bool => is_string($key) && trim($key) !== ''));
        if ($tags === [] && $keys === []) {
            throw ValidationException::withMessages(['cache' => 'At least one cache tag or key is required.']);
        }
        if (trim($idempotencyKey) === '') {
            throw ValidationException::withMessages(['idempotency_key' => 'An idempotency key is required.']);
        }

        return DB::transaction(function () use ($teamId, $tags, $keys, $idempotencyKey): CacheInvalidation {
            $existing = CacheInvalidation::query()->where('team_id', $teamId)->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
            $matched = CacheEntry::query()->where('team_id', $teamId)->when($tags, fn ($q) => $q->where(function ($q) use ($tags): void {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            }))->when($keys, fn ($q) => $q->orWhereIn('cache_key', $keys))->get();
            foreach ($matched as $entry) {
                Cache::forget($entry->cache_key);
                $entry->update(['status' => 'invalidated', 'last_invalidated_at' => now()]);
            }

            return CacheInvalidation::query()->create(['team_id' => $teamId, 'idempotency_key' => $idempotencyKey, 'tags' => $tags, 'cache_keys' => $keys, 'status' => 'completed', 'invalidated_count' => $matched->count(), 'completed_at' => now()]);
        });
    }

    /** @return array{entries:int,warm:int,hits:int,misses:int,hit_rate:float} */
    public function diagnostics(?int $teamId): array
    {
        $entries = CacheEntry::query()->where('team_id', $teamId)->get(['status', 'hits', 'misses']);
        $hits = 0;
        $misses = 0;
        foreach ($entries as $entry) {
            $hits += is_int($entry->hits) ? $entry->hits : 0;
            $misses += is_int($entry->misses) ? $entry->misses : 0;
        }

        return ['entries' => $entries->count(), 'warm' => $entries->where('status', 'warm')->count(), 'hits' => $hits, 'misses' => $misses, 'hit_rate' => $hits + $misses === 0 ? 0.0 : round($hits / ($hits + $misses), 4)];
    }

    private function validateKey(string $key): void
    {
        if (trim($key) === '' || strlen($key) > 255) {
            throw ValidationException::withMessages(['cache_key' => 'A cache key is required and must be 255 characters or fewer.']);
        }
    }
}
