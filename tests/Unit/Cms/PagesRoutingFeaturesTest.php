<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Models\PageRedirect;
use Liberu\Cms\Pages\Queries\PageTreeQuery;
use Liberu\Cms\Pages\Repositories\PageRepository;
use Liberu\Cms\Pages\Services\PageRoutingService;

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

it('builds an arbitrarily deep page tree through the query boundary', function (): void {
    $root = Page::factory()->create(['title' => 'Docs', 'parent_id' => null]);
    $child = Page::factory()->create(['title' => 'Guide', 'parent_id' => $root->id]);
    Page::factory()->create(['title' => 'Install', 'parent_id' => $child->id]);

    $tree = app(PageTreeQuery::class)->roots();

    expect($tree)->toHaveCount(1)
        ->and($tree->first()->children->first()->children)->toHaveCount(1);
});

it('centralizes alias and redirect validation in the routing service', function (): void {
    $page = Page::factory()->create(['slug' => 'landing']);
    $routing = app(PageRoutingService::class);

    expect($routing->addAlias($page, 'legacy/landing')->path)->toBe('/legacy/landing');

    expect(fn () => $routing->createRedirect([
        'from_path' => '/legacy/landing',
        'to_path' => '/landing',
    ]))->toThrow(ValidationException::class);
});

it('rejects redirect loops and unsupported status codes', function (): void {
    $routing = app(PageRoutingService::class);

    expect(fn () => $routing->createRedirect([
        'from_path' => '/same',
        'to_path' => '/same',
    ]))->toThrow(ValidationException::class);

    expect(fn () => $routing->createRedirect([
        'from_path' => '/old',
        'to_path' => '/new',
        'status_code' => 200,
    ]))->toThrow(ValidationException::class);
});
