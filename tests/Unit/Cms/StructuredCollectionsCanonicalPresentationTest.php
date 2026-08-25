<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\StructuredCollections\Actions\StructuredCollectionMutationService;
use Liberu\Cms\StructuredCollectionsFilament\Resources\StructuredCollectionResource;
use Liberu\Cms\StructuredCollectionsLivewire\Livewire\StructuredCollectionBrowser;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('exposes canonical domain, Filament, and Livewire boundaries', function (): void {
    $service = app(StructuredCollectionMutationService::class);
    $collection = $service->create(['name' => 'Directory', 'type' => 'directory']);
    $service->createRecord($collection, ['title' => 'Visible', 'status' => 'published', 'published_at' => now()]);

    expect(StructuredCollectionResource::getModel())->toBe(Collection::class);

    Livewire::test(StructuredCollectionBrowser::class, ['collection' => 'directory'])
        ->assertSuccessful()
        ->assertSee('Visible');
});
