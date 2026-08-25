<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SyndicationAndFeeds\Services\FeedService;

uses(RefreshDatabase::class);
it('renders structured feeds, imports, deduplicates, and queues syndication', function (): void {
    $service = app(FeedService::class);
    $feed = $service->create('news', 'News', 'json', 'https://source.example/feed');
    $service->addItem($feed, ['external_id' => '1', 'title' => 'Hello', 'url' => 'https://example.com/hello', 'content' => 'Body']);
    $service->addItem($feed, ['external_id' => '1', 'title' => 'Hello', 'url' => 'https://example.com/hello']);
    expect($feed->items()->count())->toBe(1)->and($service->render($feed))->toContain('jsonfeed.org')->and($service->syndicate($feed, 'https://destination.example/push')->status)->toBe('queued');
});
it('validates formats, XML, and destinations', function (): void {
    $service = app(FeedService::class);
    expect(fn () => $service->create('bad', 'Bad', 'yaml'))->toThrow(ValidationException::class);
    $feed = $service->create('rss', 'RSS');
    expect(fn () => $service->import($feed, '<bad'))->toThrow(ValidationException::class)->and(fn () => $service->syndicate($feed, 'relative'))->toThrow(ValidationException::class);
});
