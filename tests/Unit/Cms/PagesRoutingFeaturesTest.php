<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Models\PageRedirect;
use Liberu\Cms\Pages\Repositories\PageRepository;

uses(RefreshDatabase::class);

it('builds hierarchical paths and breadcrumbs', function (): void {
    $parent = Page::factory()->create(['slug' => 'docs', 'title' => 'Docs']);
    $child = Page::factory()->create(['slug' => 'install', 'title' => 'Install', 'parent_id' => $parent->id]);

    expect($child->path())->toBe('/docs/install')
        ->and(collect($child->breadcrumbs())->pluck('title')->all())->toBe(['Docs', 'Install']);
});

it('supports aliases and redirects through the page repository boundary', function (): void {
    $page = Page::factory()->create(['slug' => 'new-location']);
    $page->addAlias('/old-location');
    PageRedirect::create(['from_path' => '/legacy', 'to_path' => '/new-location', 'status_code' => 308]);

    $repository = app(PageRepository::class);

    expect($repository->findByPath('/old-location')?->is($page))->toBeTrue()
        ->and($repository->redirectForPath('/legacy')?->to_path)->toBe('/new-location');
});

it('allows only one home and error page in a tenant', function (): void {
    $first = Page::factory()->create();
    $second = Page::factory()->create();

    $first->markAsHome();
    $second->markAsHome();
    $first->markAsError();
    $second->markAsError();

    expect($first->fresh()->isHome())->toBeFalse()
        ->and($second->fresh()->isHome())->toBeTrue()
        ->and($first->fresh()->isErrorPage())->toBeFalse()
        ->and($second->fresh()->isErrorPage())->toBeTrue();
});
