<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the selected PWA status through the registered Livewire component', function (): void {
    PwaConfiguration::create(['site_key' => 'main', 'name' => 'Liberu CMS', 'short_name' => 'CMS', 'service_worker_version' => 'v1']);

    Livewire::test('module-cms-offline-and-pwa.pwa-status')
        ->set('siteKey', 'main')
        ->assertSee('Liberu CMS')
        ->assertSee('v1');
});
