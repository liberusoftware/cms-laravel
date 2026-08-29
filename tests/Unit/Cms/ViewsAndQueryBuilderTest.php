<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\ViewsAndQueryBuilder\Actions\ViewDefinitionMutationService;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ListingQueryService;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ViewDefinitionQuery;

uses(RefreshDatabase::class);

it('creates validated view definitions through the domain mutation boundary', function (): void {
    $view = app(ViewDefinitionMutationService::class)->create([
        'name' => 'Published Articles',
        'source' => 'collection_items',
        'definition' => ['fields' => ['title', 'status'], 'filters' => [['field' => 'status', 'operator' => '=', 'value' => 'published']], 'sorts' => ['title']],
        'status' => 'published',
        'published_at' => now(),
    ]);

    expect($view)->toBeInstanceOf(ViewDefinition::class)
        ->and($view->slug)->toBe('published-articles')
        ->and($view->definition['filters'][0]['field'])->toBe('status');
});

it('executes only allowlisted fields and operators', function (): void {
    $collection = Collection::create(['name' => 'Articles', 'type' => 'record']);
    $collection->items()->create(['title' => 'First', 'status' => 'published', 'published_at' => now()]);
    $collection->items()->create(['title' => 'Draft', 'status' => 'draft']);
    $view = ViewDefinition::create([
        'name' => 'Published items',
        'source' => 'collection_items',
        'definition' => ['fields' => ['title', 'status'], 'filters' => [['field' => 'status', 'operator' => '=', 'value' => 'published']], 'sorts' => ['title']],
        'status' => 'published',
        'published_at' => now(),
    ]);

    $results = app(ListingQueryService::class)->execute($view, 10);

    expect($results->total())->toBe(1)->and($results->first()->title)->toBe('First');

    $view->update(['definition' => ['fields' => ['title'], 'filters' => [['field' => 'secret', 'operator' => '=', 'value' => 'x']]]]);
    expect(fn () => app(ListingQueryService::class)->execute($view))->toThrow(ValidationException::class);
});

it('projects listing results to declared fields and the record key', function (): void {
    $collection = Collection::create(['name' => 'Private Articles', 'type' => 'record']);
    $collection->items()->create(['title' => 'Public title', 'status' => 'published', 'content' => 'Private body', 'metadata' => ['secret' => true]]);
    $view = ViewDefinition::create([
        'name' => 'Public titles',
        'source' => 'collection_items',
        'definition' => ['fields' => ['title', 'status'], 'filters' => [['field' => 'status', 'operator' => '=', 'value' => 'published']]],
    ]);

    $attributes = app(ListingQueryService::class)->execute($view)->first()->getAttributes();

    expect($attributes)->toHaveKeys(['id', 'title', 'status'])
        ->and($attributes)->not->toHaveKeys(['content', 'metadata']);
});

it('does not expose draft definitions through the public query boundary', function (): void {
    ViewDefinition::create(['name' => 'Draft', 'source' => 'collection_items', 'definition' => ['fields' => ['title']]]);
    ViewDefinition::create(['name' => 'Live', 'source' => 'collection_items', 'definition' => ['fields' => ['title']], 'status' => 'published', 'published_at' => now()]);

    expect(app(ViewDefinitionQuery::class)->publishedList()->total())->toBe(1)
        ->and(app(ViewDefinitionQuery::class)->findPublished('draft'))->toBeNull()
        ->and(app(ViewDefinitionQuery::class)->findPublished('live'))->not->toBeNull();
});

it('rejects unsafe query definitions at the mutation boundary', function (): void {
    expect(fn () => app(ViewDefinitionMutationService::class)->create([
        'name' => 'Unsafe view',
        'source' => 'collection_items',
        'definition' => [
            'fields' => ['title'],
            'filters' => [['field' => 'secret', 'operator' => '=']],
        ],
    ]))->toThrow(ValidationException::class);
});
