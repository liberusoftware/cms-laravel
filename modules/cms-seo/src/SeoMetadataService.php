<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Seo\Models\SeoMetadata;

final class SeoMetadataService
{
    public function save(string $type, int $id, array $attributes, ?int $teamId = null): SeoMetadata
    {
        if (trim($type) === '' || $id < 1) {
            throw ValidationException::withMessages(['seoable' => 'A valid SEO subject type and identifier are required.']);
        }

        return SeoMetadata::query()->updateOrCreate(['seoable_type' => $type, 'seoable_id' => $id, 'team_id' => $teamId], $this->validated($attributes));
    }

    public function find(string $type, int $id, ?int $teamId = null): ?SeoMetadata
    {
        return SeoMetadata::query()->where('seoable_type', $type)->where('seoable_id', $id)->where('team_id', $teamId)->first();
    }

    /** @return array{score:int, issues:array<int,string>, metadata:array<string,mixed>} */
    public function check(string $type, int $id, ?int $teamId = null): array
    {
        $metadata = $this->find($type, $id, $teamId);
        $issues = [];
        if (! $metadata?->title) {
            $issues[] = 'A title is required.';
        } elseif (Str::length($metadata->title) > 60) {
            $issues[] = 'The title should be 60 characters or fewer.';
        }
        if (! $metadata?->description) {
            $issues[] = 'A description is required.';
        } elseif (Str::length($metadata->description) > 160) {
            $issues[] = 'The description should be 160 characters or fewer.';
        }
        if (! $metadata?->canonical_url) {
            $issues[] = 'A canonical URL is required.';
        }

        return ['score' => max(0, 100 - count($issues) * 25), 'issues' => $issues, 'metadata' => $metadata?->toArray() ?? []];
    }

    /** @return array<string,mixed> */
    private function validated(array $attributes): array
    {
        if (isset($attributes['canonical_url']) && ! filter_var($attributes['canonical_url'], FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['canonical_url' => 'Canonical URL must be absolute.']);
        }
        if (isset($attributes['robots'])) {
            if (! is_string($attributes['robots'])) {
                throw ValidationException::withMessages(['robots' => 'Robots directives must be a string.']);
            }
            $directives = array_filter(array_map(trim(...), explode(',', strtolower($attributes['robots']))));
            $allowed = ['all', 'index', 'noindex', 'follow', 'nofollow', 'none', 'noarchive', 'nosnippet', 'noimageindex', 'notranslate'];
            if ($directives === [] || array_diff($directives, $allowed) !== [] || count($directives) !== count(array_unique($directives))) {
                throw ValidationException::withMessages(['robots' => 'Robots contains unsupported or duplicate directives.']);
            }
            $attributes['robots'] = implode(',', $directives);
        }
        $attributes['robots'] ??= 'index,follow';

        return array_intersect_key($attributes, array_flip(['title', 'description', 'canonical_url', 'robots', 'structured_data', 'social_cards', 'hreflang', 'noindex', 'noarchive']));
    }
}
