<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\TranslationManagement\Models\TranslationJob;
use Liberu\Cms\TranslationManagementFilament\Resources\TranslationJobResource;
use Liberu\Cms\TranslationManagementLivewire\TranslationManagementLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the canonical Livewire namespace and renders jobs', function (): void {
    app()->register(TranslationManagementLivewireServiceProvider::class);
    TranslationJob::create(['name' => 'Website', 'source_locale' => 'en', 'target_locale' => 'fr']);

    expect(app('livewire')->exists('module-cms-translation-management::job-browser'))->toBeTrue();

    Livewire::test('module-cms-translation-management::job-browser')
        ->assertSuccessful()
        ->assertSee('Website');
});

it('exposes the translation job Filament resource contract', function (): void {
    expect(TranslationJobResource::getModel())->toBe(TranslationJob::class)
        ->and(TranslationJobResource::getPages())->toHaveKey('index');
});
