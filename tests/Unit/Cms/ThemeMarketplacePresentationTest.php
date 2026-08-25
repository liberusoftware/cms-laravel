<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Liberu\Cms\ThemeMarketplaceFilament\Resources\MarketplaceThemeResource;
use Liberu\Cms\ThemeMarketplaceLivewire\ThemeMarketplaceLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the public theme catalog Livewire surface', function (): void {
    app()->register(ThemeMarketplaceLivewireServiceProvider::class);
    MarketplaceTheme::create(['key' => 'approved', 'name' => 'Approved Theme', 'version' => '1.0.0', 'author' => 'Test', 'license' => 'MIT', 'manifest' => [], 'status' => 'published', 'security_status' => 'approved']);
    expect(app('livewire')->exists('module-cms-theme-marketplace::theme-catalog'))->toBeTrue();
    Livewire::test('module-cms-theme-marketplace::theme-catalog')->assertSuccessful()->assertSee('Approved Theme');
});

it('exposes the marketplace Filament resource contract', function (): void {
    expect(MarketplaceThemeResource::getModel())->toBe(MarketplaceTheme::class)->and(MarketplaceThemeResource::getPages())->toHaveKey('index');
});
