<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistant\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MediaAssistant\Models\MediaSuggestion;

final class MediaAssistantService
{
    /** @param array<string, mixed> $provenance */
    public function suggest(string $assetKey, string $kind, string $value, string $provider, ?string $model = null, ?float $confidence = null, array $provenance = [], ?int $teamId = null): MediaSuggestion
    {
        $this->key($assetKey, 'asset_key');
        $this->key($value, 'value');
        if (! in_array($kind, ['alt_text', 'caption', 'transcript', 'tag', 'crop', 'rights_warning'], true)) {
            throw ValidationException::withMessages(['kind' => 'Unsupported suggestion kind.']);
        } $this->key($provider, 'provider');
        if ($confidence !== null && ($confidence < 0 || $confidence > 1)) {
            throw ValidationException::withMessages(['confidence' => 'Confidence must be between 0 and 1.']);
        }

        return MediaSuggestion::query()->create(['team_id' => $teamId, 'public_id' => (string) Str::uuid(), 'asset_key' => $assetKey, 'kind' => $kind, 'value' => $value, 'provider' => $provider, 'model' => $model, 'confidence' => $confidence, 'provenance' => $provenance, 'status' => 'pending']);
    }

    public function review(MediaSuggestion $suggestion, string $decision, string $reviewerKey, ?string $note = null, ?int $teamId = null): MediaSuggestion
    {
        if ($suggestion->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The suggestion belongs to another tenant.']);
        } $this->key($reviewerKey, 'reviewer_key');
        if (! in_array($decision, ['accepted', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Review decision is invalid.']);
        } $suggestion->update(['status' => $decision, 'reviewer_key' => $reviewerKey, 'review_note' => $note]);

        return $suggestion->refresh();
    }

    /** @return array<int, string> */
    public function acceptedTags(string $assetKey, ?int $teamId = null): array
    {
        $values = MediaSuggestion::query()
            ->where(['team_id' => $teamId, 'asset_key' => $assetKey, 'kind' => 'tag', 'status' => 'accepted'])
            ->pluck('value')
            ->all();

        return array_values(array_filter($values, is_string(...)));
    }

    private function key(string $value, string $field): void
    {
        if (trim($value) === '' || strlen($value) > 500 || str_contains($value, '..') || str_contains($value, "\0")) {
            throw ValidationException::withMessages([$field => 'The media value is invalid.']);
        }
    }
}
