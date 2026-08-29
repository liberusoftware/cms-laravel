<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionCategory;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionDistribution;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionLicense;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionListing;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionPublisher;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionReview;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionSupport;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionTrial;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionVersion;

final class ExtensionMarketplaceService
{
    public function publisher(array $attributes, ?int $teamId = null): ExtensionPublisher
    {
        $key = trim((string) ($attributes['key'] ?? ''));
        $name = trim((string) ($attributes['name'] ?? ''));
        if ($key === '' || $name === '') {
            throw ValidationException::withMessages(['publisher' => 'Publisher key and name are required.']);
        }
        if (isset($attributes['website']) && filter_var($attributes['website'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['website' => 'Publisher website must be a valid URL.']);
        }

        return ExtensionPublisher::query()->updateOrCreate(['key' => $key, 'team_id' => $teamId], ['name' => $name, 'website' => $attributes['website'] ?? null, 'status' => $attributes['status'] ?? 'pending', 'team_id' => $teamId]);
    }

    public function category(array $attributes): ExtensionCategory
    {
        $key = trim((string) ($attributes['key'] ?? ''));
        $name = trim((string) ($attributes['name'] ?? ''));
        if ($key === '' || $name === '') {
            throw ValidationException::withMessages(['category' => 'Category key and name are required.']);
        }

        return ExtensionCategory::query()->updateOrCreate(['key' => $key], ['name' => $name, 'description' => $attributes['description'] ?? null]);
    }

    public function listing(array $attributes, ?int $teamId = null): ExtensionListing
    {
        foreach (['key', 'name', 'publisher_id'] as $required) {
            if (($attributes[$required] ?? null) === null || $attributes[$required] === '') {
                throw ValidationException::withMessages([$required => 'This listing field is required.']);
            }
        }
        if (! ExtensionPublisher::query()->whereKey($attributes['publisher_id'])->exists()) {
            throw ValidationException::withMessages(['publisher_id' => 'The publisher is not available.']);
        }
        if (isset($attributes['metadata']) && ! is_array($attributes['metadata'])) {
            throw ValidationException::withMessages(['metadata' => 'Listing metadata must be an object.']);
        }

        return ExtensionListing::query()->updateOrCreate(['key' => $attributes['key'], 'team_id' => $teamId], ['publisher_id' => $attributes['publisher_id'], 'category_id' => $attributes['category_id'] ?? null, 'name' => $attributes['name'], 'description' => $attributes['description'] ?? null, 'license' => $attributes['license'] ?? 'proprietary', 'status' => $attributes['status'] ?? 'draft', 'security_status' => $attributes['security_status'] ?? 'pending', 'metadata' => $attributes['metadata'] ?? [], 'team_id' => $teamId]);
    }

    public function version(ExtensionListing $listing, array $attributes): ExtensionVersion
    {
        $release = trim((string) ($attributes['version'] ?? ''));
        if (! preg_match('/^\d+\.\d+\.\d+([-.][0-9A-Za-z.-]+)?$/', $release)) {
            throw ValidationException::withMessages(['version' => 'Extension versions must use semantic versioning.']);
        }
        if (! is_string($attributes['download_url'] ?? null) || filter_var($attributes['download_url'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['download_url' => 'A valid distribution URL is required.']);
        }
        if (! is_string($attributes['checksum'] ?? null) || ! preg_match('/^[a-f0-9]{32,128}$/i', $attributes['checksum'])) {
            throw ValidationException::withMessages(['checksum' => 'A hexadecimal release checksum is required.']);
        }

        return ExtensionVersion::query()->updateOrCreate(['listing_id' => $listing->id, 'version' => $release], ['download_url' => $attributes['download_url'], 'checksum' => strtolower($attributes['checksum']), 'signature' => $attributes['signature'] ?? null, 'signing_key' => $attributes['signing_key'] ?? null, 'status' => $attributes['status'] ?? 'draft', 'released_at' => ($attributes['status'] ?? 'draft') === 'released' ? now() : null]);
    }

    public function sign(ExtensionVersion $version, string $signature, string $key): ExtensionVersion
    {
        if (trim($signature) === '' || trim($key) === '') {
            throw ValidationException::withMessages(['signature' => 'A signature and signing key are required.']);
        }
        $version->update(['signature' => $signature, 'signing_key' => $key]);

        return $version->refresh();
    }

    public function security(ExtensionListing $listing, string $status): ExtensionListing
    {
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['security_status' => 'Unsupported security review status.']);
        }
        $listing->update(['security_status' => $status]);

        return $listing->refresh();
    }

    public function review(ExtensionListing $listing, string $type, int|string $subject, int $rating, ?string $body = null, ?int $teamId = null): ExtensionReview
    {
        if ($rating < 1 || $rating > 5 || trim($type) === '') {
            throw ValidationException::withMessages(['rating' => 'A reviewer type and rating from 1 to 5 are required.']);
        }

        return ExtensionReview::query()->updateOrCreate(['listing_id' => $listing->id, 'reviewer_type' => $type, 'reviewer_id' => (string) $subject, 'team_id' => $teamId], ['rating' => $rating, 'review' => $body, 'status' => 'published', 'team_id' => $teamId]);
    }

    public function license(ExtensionListing $listing, string $subjectType, int|string $subject, ?int $days = null): ExtensionLicense
    {
        if (trim($subjectType) === '') {
            throw ValidationException::withMessages(['subject_type' => 'A license subject is required.']);
        }
        $key = (string) Str::uuid();

        return ExtensionLicense::query()->create(['listing_id' => $listing->id, 'license_key' => $key, 'subject_type' => $subjectType, 'subject_id' => (string) $subject, 'status' => 'active', 'starts_at' => now(), 'ends_at' => $days === null ? null : now()->addDays(max(1, $days))]);
    }

    public function trial(ExtensionListing $listing, string $subjectType, int|string $subject, int $days = 14): ExtensionTrial
    {
        if ($days < 1 || $days > 365 || trim($subjectType) === '') {
            throw ValidationException::withMessages(['days' => 'Trials must last between 1 and 365 days.']);
        }

        return ExtensionTrial::query()->firstOrCreate(['listing_id' => $listing->id, 'subject_type' => $subjectType, 'subject_id' => (string) $subject], ['status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addDays($days)]);
    }

    public function support(ExtensionListing $listing, array $attributes): ExtensionSupport
    {
        $channel = trim((string) ($attributes['channel'] ?? ''));
        if ($channel === '') {
            throw ValidationException::withMessages(['channel' => 'A support channel is required.']);
        }

        return ExtensionSupport::query()->updateOrCreate(['listing_id' => $listing->id, 'channel' => $channel], ['url' => $attributes['url'] ?? null, 'response_hours' => $attributes['response_hours'] ?? null]);
    }

    public function distribute(ExtensionVersion $version, array $attributes): ExtensionDistribution
    {
        if (filter_var($attributes['url'] ?? null, FILTER_VALIDATE_URL) === false || ! preg_match('/^[a-f0-9]{32,128}$/i', (string) ($attributes['checksum'] ?? ''))) {
            throw ValidationException::withMessages(['distribution' => 'A valid URL and hexadecimal checksum are required.']);
        }

        return ExtensionDistribution::query()->updateOrCreate(['version_id' => $version->id, 'channel' => $attributes['channel'] ?? 'stable'], ['url' => $attributes['url'], 'checksum' => strtolower($attributes['checksum']), 'status' => $attributes['status'] ?? 'available']);
    }

    /** @return array{average: float, count: int} */
    public function ratingSummary(ExtensionListing $listing): array
    {
        return ['average' => round((float) $listing->reviews()->where('status', 'published')->avg('rating'), 2), 'count' => $listing->reviews()->where('status', 'published')->count()];
    }

    public function publish(ExtensionListing $listing): ExtensionListing
    {
        if ($listing->security_status !== 'approved' || ! $listing->versions()->where('status', 'released')->whereNotNull('signature')->exists()) {
            throw ValidationException::withMessages(['listing' => 'Only security-approved listings with a signed release may be published.']);
        }

        return DB::transaction(function () use ($listing): ExtensionListing {
            $listing->update(['status' => 'published']);

            return $listing->refresh();
        });
    }
}
