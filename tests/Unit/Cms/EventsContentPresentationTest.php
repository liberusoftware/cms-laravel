<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\EventsContent\Models\Event;
use Liberu\Cms\EventsContentFilament\Resources\EventResource;
use Liberu\Cms\EventsContentLivewire\EventsContentLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the events Livewire calendar surface', function (): void {
    app()->register(EventsContentLivewireServiceProvider::class);
    Event::query()->create(['key' => 'summit', 'title' => 'CMS Summit', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'status' => 'published']);
    expect(app('livewire')->exists('module-cms-events-content::event-calendar'))->toBeTrue();
    Livewire::test('module-cms-events-content::event-calendar')->assertSuccessful()->assertSee('CMS Summit');
});

it('exposes the events Filament resource contract', function (): void {
    expect(EventResource::getModel())->toBe(Event::class)->and(EventResource::getPages())->toHaveKey('index');
});
