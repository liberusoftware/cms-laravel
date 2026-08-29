<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionListing;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionPublisher;
use Liberu\Cms\ExtensionMarketplaceFilament\Resources\ExtensionListingResource;
use Liberu\Cms\ExtensionMarketplaceLivewire\ExtensionMarketplaceLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the approved extension catalog Livewire surface', function (): void {
    app()->register(ExtensionMarketplaceLivewireServiceProvider::class);
    $publisher = ExtensionPublisher::query()->create(['key' => 'p', 'name' => 'Publisher']);
    ExtensionListing::query()->create(['publisher_id' => $publisher->id, 'key' => 'approved', 'name' => 'Approved', 'status' => 'published', 'security_status' => 'approved']);

    expect(app('livewire')->exists('module-cms-extension-marketplace::extension-catalog'))->toBeTrue();
    Livewire::test('module-cms-extension-marketplace::extension-catalog')->assertSuccessful()->assertSee('Approved');
});

it('exposes the marketplace Filament resource contract', function (): void {
    expect(ExtensionListingResource::getModel())->toBe(ExtensionListing::class)->and(ExtensionListingResource::getPages())->toHaveKey('index');
});
