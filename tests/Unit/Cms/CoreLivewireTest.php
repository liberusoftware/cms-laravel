<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\CoreLivewire\Livewire\ChannelList;
use Liberu\Cms\CoreLivewire\CoreLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('browses channels through the Core Livewire query boundary', function (): void {
    app()->register(CoreLivewireServiceProvider::class);
    expect(app('livewire')->exists('module-cms-core::channel-list'))->toBeTrue();

    $site = Site::create(['key' => 'docs', 'name' => 'Docs']);
    $site->channels()->create(['key' => 'web', 'name' => 'Website']);
    $site->channels()->create(['key' => 'api', 'name' => 'Public API']);

    Livewire::test('module-cms-core::channel-list', ['site' => 'docs'])
        ->set('search', 'api')
        ->assertSee('Public API')
        ->assertDontSee('Website');
});
