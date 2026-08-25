<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;
use Liberu\Cms\WebDeliveryFilament\Resources\DeliveryRouteResource;
use Liberu\Cms\WebDeliveryLivewire\WebDeliveryLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the delivery preview Livewire surface', function (): void {
    app()->register(WebDeliveryLivewireServiceProvider::class);
    DeliveryRoute::create(['path' => '/home', 'body' => 'Welcome', 'status' => 'published']);

    expect(app('livewire')->exists('module-cms-web-delivery::route-preview'))->toBeTrue();
    Livewire::test('module-cms-web-delivery::route-preview', ['path' => '/home'])
        ->call('resolve')
        ->assertSee('Status: 200')
        ->assertSee('Welcome');
});

it('exposes the delivery route Filament resource contract', function (): void {
    expect(DeliveryRouteResource::getModel())->toBe(DeliveryRoute::class)
        ->and(DeliveryRouteResource::getPages())->toHaveKey('index');
});
