<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Experimentation\Models\Experiment;
use Liberu\Cms\ExperimentationFilament\Resources\ExperimentResource;
use Liberu\Cms\ExperimentationLivewire\ExperimentationLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the experimentation Livewire surface', function (): void {
    app()->register(ExperimentationLivewireServiceProvider::class);
    Experiment::query()->create(['key' => 'hero', 'name' => 'Hero', 'variants' => []]);
    expect(app('livewire')->exists('module-cms-experimentation::experiment-list'))->toBeTrue();
    Livewire::test('module-cms-experimentation::experiment-list')->assertSuccessful()->assertSee('Hero');
});

it('exposes the experimentation Filament resource contract', function (): void {
    expect(ExperimentResource::getModel())->toBe(Experiment::class)->and(ExperimentResource::getPages())->toHaveKey('index');
});
