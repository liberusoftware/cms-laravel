<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ForumsIntegration\Models\ForumReference;

final readonly class ForumsIntegrationService
{
    public function __construct(private ForumProviderRegistry $providers) {}

    /** @param array<string, mixed> $metadata */
    public function link(string $provider, string $externalType, string $externalId, ?string $url = null, ?int $teamId = null, array $metadata = []): ForumReference
    {
        $this->key($provider, 'provider');
        $this->key($externalType, 'external_type');
        $this->key($externalId, 'external_id');
        if ($url !== null && (! filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https')) {
            throw ValidationException::withMessages(['url' => 'Forum URLs must use HTTPS.']);
        }

        return ForumReference::query()->updateOrCreate(['team_id' => $teamId, 'provider' => $provider, 'external_type' => $externalType, 'external_id' => $externalId], ['public_id' => (string) Str::uuid(), 'url' => $url, 'metadata' => $metadata, 'last_activity_at' => now()]);
    }

    /** @return list<array<string, scalar|null>> */
    public function recent(string $provider, ?int $teamId = null, int $limit = 10): array
    {
        if ($limit < 1 || $limit > 100) {
            throw ValidationException::withMessages(['limit' => 'The discussion limit is invalid.']);
        }

        return $this->providers->get($provider)->recentDiscussions($teamId, $limit);
    }

    public function moderationUrl(ForumReference $reference, ?int $teamId = null): ?string
    {
        $this->tenant($reference, $teamId);

        return $this->providers->get($reference->provider)->moderationUrl($reference->external_type, $reference->external_id);
    }

    /** @return array<string, scalar|null> */
    public function sso(string $provider, string $subjectKey, ?int $teamId = null): array
    {
        $this->key($subjectKey, 'subject_key');

        return $this->providers->get($provider)->ssoContext($subjectKey, $teamId);
    }

    private function tenant(ForumReference $reference, ?int $teamId): void
    {
        if ($reference->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The forum reference belongs to another tenant.']);
        }
    }

    private function key(string $value, string $field): void
    {
        if (trim($value) === '' || strlen($value) > 180 || str_contains($value, '..') || str_contains($value, "\0")) {
            throw ValidationException::withMessages([$field => 'The forum value is invalid.']);
        }
    }
}
