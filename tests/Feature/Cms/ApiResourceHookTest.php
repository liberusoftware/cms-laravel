<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Hooks\Filters\ApiResourceFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Pages\Http\Resources\PageResource;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Posts\Models\Post;

uses(RefreshDatabase::class);

it('lets an extension reshape a Delivery API payload via ApiResourceFilter', function (): void {
    app(HookBusInterface::class)->listen(ApiResourceFilter::class, function (ApiResourceFilter $filter): void {
        if ($filter->model instanceof Page) {
            $filter->data['injected'] = 'yes';
            unset($filter->data['template']);
        }
    });

    $page = Page::factory()->create(['template' => 'default']);

    $payload = (new PageResource($page))->toArray(request());

    expect($payload)->toHaveKey('injected', 'yes')
        ->and($payload)->not->toHaveKey('template');
});

it('scopes the API resource hook to the targeted model type', function (): void {
    app(HookBusInterface::class)->listen(ApiResourceFilter::class, function (ApiResourceFilter $filter): void {
        if ($filter->model instanceof Post) {
            $filter->data['injected'] = 'yes';
        }
    });

    $page = Page::factory()->create();

    expect((new PageResource($page))->toArray(request()))->not->toHaveKey('injected');
});

it('returns the API payload unchanged when no resource hook is registered', function (): void {
    $page = Page::factory()->create(['template' => 'default']);

    expect((new PageResource($page))->toArray(request()))
        ->toHaveKey('template', 'default')
        ->not->toHaveKey('injected');
});
