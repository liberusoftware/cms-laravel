<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Search\Index\DatabaseSearchIndex;
use Liberu\Cms\Search\Index\ScoutSearchIndex;
use Tests\Fixtures\ScoutThingSource;
use Tests\Fixtures\SearchableThing;

uses(RefreshDatabase::class);

/**
 * Re-resolve the driver after the config has been changed at runtime.
 */
function activeSearchIndex(): SearchIndexInterface
{
    app()->forgetInstance(SearchIndexInterface::class);

    return app(SearchIndexInterface::class);
}

it('selects the database driver by default', function (): void {
    config(['cms-search.driver' => 'database']);

    expect(activeSearchIndex())->toBeInstanceOf(DatabaseSearchIndex::class);
});

it('selects the scout driver when configured', function (): void {
    config(['cms-search.driver' => 'scout']);

    expect(activeSearchIndex())->toBeInstanceOf(ScoutSearchIndex::class);
});

it('reports the database driver ready when the connection is reachable', function (): void {
    config(['cms-search.driver' => 'database']);

    expect(activeSearchIndex()->isReady())->toBeTrue();
});

it('runs a query through the scout collection engine and returns hits', function (): void {
    config(['cms-search.driver' => 'scout', 'scout.driver' => 'collection']);

    Schema::create('searchable_things', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('title');
    });

    SearchableThing::create(['title' => 'Laravel Guide']);
    SearchableThing::create(['title' => 'Cooking Basics']);

    app(SearchRegistryInterface::class)->registerSource(new ScoutThingSource);

    $index = activeSearchIndex();

    $results = [];

    foreach ($index->search('Laravel') as $result) {
        $results[] = $result;
    }

    expect($index)->toBeInstanceOf(ScoutSearchIndex::class)
        ->and($index->isReady())->toBeTrue()
        ->and($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(SearchResult::class)
        ->and($results[0]->title)->toBe('Laravel Guide');
});
