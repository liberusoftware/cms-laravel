<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsApi\Http\Resources;

use Liberu\Cms\Sitemaps\Models\SitemapEntry;

final class SitemapEntryResource
{
    /** @return array<string,mixed> */
    public static function make(SitemapEntry $entry): array
    {
        return ['id' => (string) $entry->getKey(), 'type' => 'cms-sitemap-entry', 'site_id' => $entry->site_id, 'url' => $entry->url, 'entry_type' => $entry->type, 'locale' => $entry->locale, 'last_modified' => $entry->last_modified?->toISOString(), 'priority' => (float) $entry->priority, 'change_frequency' => $entry->change_frequency, 'images' => $entry->images ?? [], 'video' => $entry->video ?? [], 'news' => $entry->news ?? [], 'excluded' => (bool) $entry->excluded];
    }
}
