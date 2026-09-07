<?php

declare(strict_types=1);

namespace Liberu\Cms\Localization\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Localization\Models\Locale;
use Liberu\Cms\Localization\Models\LocaleVariant;

final class LocalizationService
{
    public function locale(string $locale, ?int $teamId = null, ?string $fallback = null, string $direction = 'ltr'): Locale
    {
        $this->localeCode($locale);
        if ($fallback !== null) {
            $this->localeCode($fallback);
        } if (! in_array($direction, ['ltr', 'rtl'], true)) {
            throw ValidationException::withMessages(['direction' => 'Direction must be ltr or rtl.']);
        }

        return Locale::query()->updateOrCreate(['team_id' => $teamId, 'locale' => $locale], ['fallback_locale' => $fallback, 'direction' => $direction, 'enabled' => true]);
    }

    public function variant(string $sourceType, string $sourceKey, string $field, string $locale, string $value, ?int $teamId = null, ?string $slug = null, string $status = 'draft'): LocaleVariant
    {
        foreach (['sourceType' => $sourceType, 'sourceKey' => $sourceKey, 'field' => $field, 'locale' => $locale, 'value' => $value] as $key => $item) {
            $this->required($item, $key);
        } if (! in_array($status, ['draft', 'complete'], true)) {
            throw ValidationException::withMessages(['status' => 'Translation status is invalid.']);
        } $this->localeCode($locale);

        return LocaleVariant::query()->updateOrCreate(['team_id' => $teamId, 'source_type' => $sourceType, 'source_key' => $sourceKey, 'field' => $field, 'locale' => $locale], ['public_id' => (string) Str::uuid(), 'value' => $value, 'localized_slug' => $slug, 'status' => $status, 'completed_at' => $status === 'complete' ? now() : null]);
    }

    public function resolve(string $sourceType, string $sourceKey, string $field, string $locale, ?int $teamId = null, ?string $fallback = null): ?LocaleVariant
    {
        $query = LocaleVariant::query()->where(['team_id' => $teamId, 'source_type' => $sourceType, 'source_key' => $sourceKey, 'field' => $field]);
        $found = (clone $query)->where('locale', $locale)->first();
        if ($found !== null || $fallback === null) {
            return $found;
        }

        return $query->where('locale', $fallback)->first();
    }

    public function completeness(string $sourceType, string $sourceKey, string $locale, ?int $teamId = null): float
    {
        $query = LocaleVariant::query()->where(['team_id' => $teamId, 'source_type' => $sourceType, 'source_key' => $sourceKey, 'locale' => $locale]);
        $total = (clone $query)->count();

        return $total === 0 ? 0.0 : round(((clone $query)->where('status', 'complete')->count() / $total) * 100, 2);
    }

    private function localeCode(string $locale): void
    {
        if (! preg_match('/^[a-z]{2,3}(?:[-_][A-Z]{2})?$/', $locale)) {
            throw ValidationException::withMessages(['locale' => 'Locale must use a valid BCP-47 style code.']);
        }
    }

    private function required(mixed $value, string $field): void
    {
        if (! is_string($value) || trim($value) === '' || strlen($value) > 255) {
            throw ValidationException::withMessages([$field => 'A non-empty value is required.']);
        }
    }
}
