<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\WebDelivery\Actions\WebDeliveryService;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;
use Liberu\Cms\WebDelivery\Support\EdgeInvalidationRegistry;

uses(RefreshDatabase::class);

final class RecordingEdgeInvalidation implements EdgeInvalidationRegistry
{
    public int $calls = 0;
    public function invalidate(\Liberu\Cms\WebDelivery\Models\DeliveryInvalidation $invalidation): void { $this->calls++; }
}

it('renders published content, preview tokens, redirects, maintenance, and edge invalidations', function (): void {
    Event::fake();
    $edge = new RecordingEdgeInvalidation;
    app()->instance(EdgeInvalidationRegistry::class, $edge);
    $service = app(WebDeliveryService::class);

    $route = $service->registerRoute(['path' => '/docs/start', 'body' => 'Hello', 'metadata' => ['title' => 'Start'], 'cache_tags' => ['content:1'], 'status' => 'published']);
    $result = $service->render('/docs/start');
    expect($result->status)->toBe(200)->and($result->body)->toBe('Hello')->and($result->metadata['title'])->toBe('Start');

    $draft = $service->registerRoute(['path' => '/preview', 'body' => 'Draft', 'status' => 'draft']);
    expect($service->render('/preview')->status)->toBe(404);
    $token = $service->issuePreviewToken($draft);
    expect($service->render('/preview', $token)->status)->toBe(200)
        ->and($service->render('/preview', 'invalid')->status)->toBe(404);

    $redirect = $service->registerRoute(['path' => '/old', 'route_type' => 'redirect', 'redirect_url' => 'https://example.test/new', 'redirect_status' => 301, 'status' => 'published']);
    expect($service->render($redirect->path)->redirectUrl)->toBe('https://example.test/new');
    $service->setMaintenance($route, true);
    expect($service->render($route->path)->status)->toBe(503);

    $first = $service->invalidate(['content:1'], 'invalidate-1', 'test');
    $second = $service->invalidate(['content:1'], 'invalidate-1', 'test');
    expect($first->status)->toBe('completed')->and($second->is($first))->toBeTrue()->and($edge->calls)->toBe(1);
});

it('rejects unsafe paths and unsupported redirects', function (): void {
    $service = app(WebDeliveryService::class);
    expect(fn () => $service->registerRoute(['path' => '/../secret']))->toThrow(ValidationException::class)
        ->and(fn () => $service->registerRoute(['path' => '/bad', 'route_type' => 'redirect', 'redirect_url' => 'https://example.test']))->toThrow(ValidationException::class);
});
