<?php

declare(strict_types=1);

namespace Liberu\Cms\Sitemaps\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Sitemaps\Models\SitemapEntry;

final class SitemapService
{
    public function add(string $url, ?int $siteId = null, string $type = 'web', ?string $locale = null, float $priority = .5, array $extensions = [], ?int $teamId = null): SitemapEntry
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['url' => 'Sitemap URLs must be absolute.']);
        } if ($priority < 0 || $priority > 1) {
            throw ValidationException::withMessages(['priority' => 'Priority must be between 0 and 1.']);
        } $entry = SitemapEntry::query()->updateOrCreate(['site_id' => $siteId, 'type' => $type, 'locale' => $locale, 'url' => $url], ['priority' => $priority, 'images' => $extensions['images'] ?? [], 'video' => $extensions['video'] ?? [], 'news' => $extensions['news'] ?? [], 'excluded' => false, 'team_id' => $teamId]);
        Cache::tags(['cms-sitemaps'])->flush();

        return $entry;
    }

    public function exclude(string $url, ?int $siteId = null): int
    {
        return SitemapEntry::query()->where('url', $url)->where('site_id', $siteId)->update(['excluded' => true]);
    }

    /** @return array<int, SitemapEntry> */
    public function entries(?int $siteId = null, ?string $type = null, ?string $locale = null): array
    {
        return Cache::tags(['cms-sitemaps'])->remember('cms-sitemap:'.sha1(serialize([$siteId, $type, $locale])), 300, fn (): array => SitemapEntry::query()->where('site_id', $siteId)->when($type, fn ($query) => $query->where('type', $type))->when($locale, fn ($query) => $query->where('locale', $locale))->where('excluded', false)->orderBy('url')->get()->all());
    }

    /** @return array<int, array<int, SitemapEntry>> */
    public function chunks(?int $siteId = null, int $size = 50000): array
    {
        return array_chunk($this->entries($siteId), max(1, min(50000, $size)));
    }

    public function notify(string $engine, ?int $siteId = null): array
    {
        if (! in_array($engine, ['google', 'bing'], true)) {
            throw ValidationException::withMessages(['engine' => 'Unsupported search engine.']);
        }

return ['engine' => $engine, 'url' => url('/sitemap.xml'), 'entries' => count($this->entries($siteId)), 'queued' => true];
    }
}
