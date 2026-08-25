<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;
use Liberu\Cms\ViewsAndQueryBuilderFilament\Resources\ViewDefinitionResource;
use Liberu\Cms\ViewsAndQueryBuilderLivewire\ViewsAndQueryBuilderLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the canonical Livewire namespace and renders a published view', function (): void {
    app()->register(ViewsAndQueryBuilderLivewireServiceProvider::class);

    $collection = Collection::create(['name' => 'Articles']);
    $collection->items()->create(['title' => 'Visible', 'status' => 'published', 'published_at' => now()]);
    ViewDefinition::create([
        'name' => 'Articles',
        'source' => 'collection_items',
        'definition' => ['fields' => ['title']],
        'status' => 'published',
        'published_at' => now(),
    ]);

    expect(app('livewire')->exists('module-cms-views-and-query-builder::view-browser'))->toBeTrue();

    Livewire::test('module-cms-views-and-query-builder::view-browser', ['view' => 'articles'])
        ->assertSuccessful()
        ->assertSee('Visible');
});

it('exposes the view definition through the Filament resource contract', function (): void {
    expect(ViewDefinitionResource::getModel())->toBe(ViewDefinition::class)
        ->and(ViewDefinitionResource::getPages())->toHaveKey('index');
});
