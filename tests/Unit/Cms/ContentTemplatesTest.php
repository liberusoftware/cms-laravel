<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentTemplates\Services\ContentTemplatesService;

uses(RefreshDatabase::class);

it('creates versioned templates and selects published rollout content', function (): void {
    $service = app(ContentTemplatesService::class);
    $template = $service->create(['name' => 'Article starter', 'slug' => 'article-starter', 'content_type' => 'article', 'schema' => ['fields' => ['title']], 'defaults' => ['title' => '']], 3);
    $service->publish($template);

    expect($service->select('article', 3, 0)?->id)->toBe($template->id)
        ->and($template->fresh()->published)->toBeTrue();
});

it('validates rollout and supports template locking', function (): void {
    $service = app(ContentTemplatesService::class);

    expect(fn () => $service->create(['name' => 'Bad', 'slug' => 'bad', 'content_type' => 'page', 'schema' => [], 'rollout_percent' => 101], 3))
        ->toThrow(ValidationException::class);

    $template = $service->create(['name' => 'Locked', 'slug' => 'locked', 'content_type' => 'page', 'schema' => ['fields' => []]], 3);
    expect($service->lock($template)->locked)->toBeTrue();
});
