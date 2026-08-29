<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SyndicationAndFeeds\Models\Feed;
use Liberu\Cms\SyndicationAndFeeds\Models\FeedItem;
use Liberu\Cms\SyndicationAndFeeds\Models\SyndicationDelivery;

final class FeedService
{
    public function create(string $key, string $title, string $format = 'rss', ?string $sourceUrl = null, array $mapping = []): Feed
    {
        if (trim($key) === '' || trim($title) === '') {
            throw ValidationException::withMessages(['title' => 'Feed key and title are required.']);
        }
        if (! in_array($format, ['rss', 'atom', 'json'], true)) {
            throw ValidationException::withMessages(['format' => 'Unsupported feed format.']);
        }
        if ($sourceUrl !== null && filter_var($sourceUrl, FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['source_url' => 'Source URL must be valid.']);
        }

        return Feed::query()->create(['key' => Str::slug($key), 'title' => $title, 'format' => $format, 'source_url' => $sourceUrl, 'mapping' => $mapping, 'active' => true]);
    }

    public function addItem(Feed $feed, array $item): FeedItem
    {
        foreach (['external_id', 'title', 'url'] as $field) {
            if (empty($item[$field])) {
                throw ValidationException::withMessages([$field => 'Feed item field is required.']);
            }
        } $hash = hash('sha256', $item['external_id'].'|'.$item['url']);

        return FeedItem::query()->updateOrCreate(['feed_id' => $feed->getKey(), 'dedupe_hash' => $hash], ['external_id' => $item['external_id'], 'title' => $item['title'], 'url' => $item['url'], 'summary' => $item['summary'] ?? null, 'content' => $item['content'] ?? null, 'attribution' => $item['attribution'] ?? ['source' => 'cms'], 'payload' => $item, 'published_at' => $item['published_at'] ?? now()]);
    }

    public function update(Feed $feed, array $attributes): Feed
    {
        $format = $attributes['format'] ?? $feed->format;
        if (! is_string($format) || ! in_array($format, ['rss', 'atom', 'json'], true)) {
            throw ValidationException::withMessages(['format' => 'Unsupported feed format.']);
        }
        if (array_key_exists('source_url', $attributes) && $attributes['source_url'] !== null && filter_var($attributes['source_url'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['source_url' => 'Source URL must be valid.']);
        }
        $feed->update(array_intersect_key($attributes, array_flip(['title', 'format', 'source_url', 'mapping', 'active'])));

        return $feed->refresh();
    }

    public function remove(Feed $feed): void
    {
        $feed->update(['active' => false]);
    }

    public function import(Feed $feed, string $xml): int
    {
        $parsed = @simplexml_load_string($xml);
        if ($parsed === false) {
            throw ValidationException::withMessages(['xml' => 'Invalid feed XML.']);
        } $count = 0;
        foreach ($parsed->channel->item ?? $parsed->entry ?? [] as $entry) {
            $this->addItem($feed, ['external_id' => (string) ($entry->guid ?: $entry->id ?: $entry->link), 'title' => (string) $entry->title, 'url' => (string) ($entry->link['href'] ?: $entry->link), 'summary' => (string) ($entry->description ?: $entry->summary), 'content' => (string) ($entry->content ?: $entry->encoded), 'attribution' => ['source' => $feed->source_url]]);
            $count++;
        }

        return $count;
    }

    public function render(Feed $feed): string
    {
        $items = $feed->items()->latest('published_at')->get();
        if ($feed->format === 'json') {
            return json_encode(['version' => 'https://jsonfeed.org/version/1.1', 'title' => $feed->title, 'items' => $items->map(fn (FeedItem $item): array => ['id' => $item->external_id, 'url' => $item->url, 'title' => $item->title, 'content_text' => $item->content])->all()], JSON_THROW_ON_ERROR);
        }
        $body = $items->map(fn (FeedItem $item): string => '<item><guid>'.e($item->external_id).'</guid><title>'.e($item->title).'</title><link>'.e($item->url).'</link><description>'.e($item->summary).'</description></item>')->implode('');

        return $feed->format === 'atom' ? '<feed xmlns="http://www.w3.org/2005/Atom"><title>'.e($feed->title).'</title>'.$body.'</feed>' : '<rss version="2.0"><channel><title>'.e($feed->title).'</title>'.$body.'</channel></rss>';
    }

    public function syndicate(Feed $feed, string $destination): SyndicationDelivery
    {
        if (! filter_var($destination, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['destination' => 'Destination must be a URL.']);
        }

        return SyndicationDelivery::query()->create(['feed_id' => $feed->getKey(), 'destination' => $destination, 'status' => 'queued', 'response' => ['item_count' => $feed->items()->count()]]);
    }
}
