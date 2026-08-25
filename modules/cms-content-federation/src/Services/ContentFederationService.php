<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentFederation\Models\FederationReference;
use Liberu\Cms\ContentFederation\Models\FederationSource;

final readonly class ContentFederationService
{
    public function sources(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        return FederationSource::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, (int) config('content-federation.pagination.max', 100))));
    }

    public function source(array $data, ?int $teamId = null): FederationSource
    {
        if (blank($data['name'] ?? null) || blank($data['adapter'] ?? null)) {
            throw ValidationException::withMessages(['source' => 'A source name and adapter are required.']);
        }

        return FederationSource::query()->create([...$data, 'team_id' => $teamId]);
    }

    /** @param array<string, mixed> $payload */
    public function ingest(FederationSource $source, string $externalType, string $externalKey, array $payload, ?string $etag = null, ?int $cacheMinutes = null): FederationReference
    {
        if ($externalType === '' || $externalKey === '' || $payload === []) {
            throw ValidationException::withMessages(['reference' => 'A type, key, and non-empty payload are required.']);
        }
        $reference = FederationReference::query()->updateOrCreate(['source_id' => $source->id, 'external_type' => $externalType, 'external_key' => $externalKey], ['payload' => $payload, 'etag' => $etag, 'cached_until' => now()->addMinutes($cacheMinutes ?? (int) config('content-federation.default_cache_minutes', 60)), 'last_fetched_at' => now()]);
        $source->update(['status' => 'healthy', 'last_checked_at' => now(), 'last_succeeded_at' => now(), 'last_error' => null]);

        return $reference;
    }

    public function fallback(FederationSource $source, string $externalType, string $externalKey): ?FederationReference
    {
        $reference = FederationReference::query()->where(['source_id' => $source->id, 'external_type' => $externalType, 'external_key' => $externalKey])->first();
        $source->update(['status' => 'degraded', 'last_checked_at' => now(), 'last_error' => 'Remote source unavailable; serving cached content.']);

        return $reference;
    }

    public function revalidate(FederationReference $reference): bool
    {
        return $reference->cached_until === null || $reference->cached_until->isFuture();
    }
}
