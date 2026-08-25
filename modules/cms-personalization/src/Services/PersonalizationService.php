<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Models\Variant;

final class PersonalizationService
{
    public function createAudience(array $attributes, ?int $teamId = null): Audience
    {
        $this->validateAudience($attributes);

        return Audience::create(array_merge($attributes, ['team_id' => $teamId ?? ($attributes['team_id'] ?? null)]));
    }

    public function updateAudience(Audience $audience, array $attributes): Audience
    {
        $this->validateAudience($attributes, $audience);
        $audience->fill($attributes)->save();

        return $audience->refresh();
    }

    public function saveVariant(Audience $audience, array $attributes, ?Variant $variant = null): Variant
    {
        $this->validateVariant($audience, $attributes, $variant);
        if (($attributes['fallback'] ?? $variant?->fallback ?? false) === true) {
            $audience->variants()->where('fallback', true)->when($variant !== null, fn ($query) => $query->where('id', '!=', $variant->getKey()))->update(['fallback' => false]);
        }

        if ($variant === null) {
            return $audience->variants()->create(array_merge($attributes, ['team_id' => $audience->team_id]));
        }

        $variant->fill($attributes)->save();

        return $variant->refresh();
    }

    public function deleteVariant(Variant $variant): void
    {
        $variant->delete();
    }

    private function validateAudience(array $attributes, ?Audience $audience = null): void
    {
        if ($audience === null && (! isset($attributes['name'], $attributes['key']))) {
            throw ValidationException::withMessages(['audience' => 'An audience name and key are required.']);
        }
        foreach (['name', 'key'] as $field) {
            if (array_key_exists($field, $attributes) && trim((string) $attributes[$field]) === '') {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if (isset($attributes['key']) && ! preg_match('/^[a-z0-9][a-z0-9-]*$/', (string) $attributes['key'])) {
            throw ValidationException::withMessages(['key' => 'Audience keys must use lowercase letters, numbers, and hyphens.']);
        }
        if (isset($attributes['rules']) && ! is_array($attributes['rules'])) {
            throw ValidationException::withMessages(['rules' => 'Audience rules must be an array.']);
        }
    }

    private function validateVariant(Audience $audience, array $attributes, ?Variant $variant = null): void
    {
        if ($variant === null && (! isset($attributes['key']) || ! array_key_exists('payload', $attributes))) {
            throw ValidationException::withMessages(['variant' => 'A variant key and payload are required.']);
        }
        if (isset($attributes['key']) && trim((string) $attributes['key']) === '') {
            throw ValidationException::withMessages(['key' => 'A variant key is required.']);
        }
        if (isset($attributes['payload']) && ! is_array($attributes['payload'])) {
            throw ValidationException::withMessages(['payload' => 'Variant payload must be an array.']);
        }
        $holdout = (int) ($attributes['holdout_percent'] ?? $variant?->holdout_percent ?? 0);
        if ($holdout < 0 || $holdout > 100) {
            throw ValidationException::withMessages(['holdout_percent' => 'Holdout percentage must be between 0 and 100.']);
        }
        if ($audience->variants()->where('fallback', true)->count() > 1 && ($attributes['fallback'] ?? false) === true) {
            throw ValidationException::withMessages(['fallback' => 'An audience can have only one fallback variant.']);
        }
    }
}
