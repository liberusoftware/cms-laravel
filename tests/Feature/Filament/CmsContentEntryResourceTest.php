<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Filament\ContentEntryResource;
use Liberu\Cms\ContentTypes\Filament\Pages\ListContentEntries;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['content-entries.view', 'content-entries.create', 'content-entries.update', 'content-entries.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    // Livewire::test() does not run Panel::boot(), so register the tenancy scope
    // and creation observer the way a real request would.
    ContentEntryResource::registerTenancyModelGlobalScope($panel);
    ContentEntryResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

it('renders the content entries list', function (): void {
    Livewire::test(ListContentEntries::class)->assertSuccessful();
});

it('lists content entry records', function (): void {
    $entries = ContentEntry::factory()->count(3)->create();

    Livewire::test(ListContentEntries::class)->assertCanSeeTableRecords($entries);
});

it('creates an entry with data driven by the type schema', function (): void {
    $type = ContentType::factory()->create([
        'fields' => [
            ['name' => 'headline', 'label' => 'Headline', 'type' => 'text', 'required' => false, 'options' => []],
        ],
    ]);

    Livewire::test(ListContentEntries::class)
        ->callAction('create', [
            'content_type_id' => $type->id,
            'title' => 'My First Entry',
            'status' => 'draft',
            'data' => ['headline' => 'Big News'],
        ]);

    $entry = ContentEntry::query()->where('title', 'My First Entry')->first();

    expect($entry)->not->toBeNull()
        ->and($entry->content_type_id)->toBe($type->id)
        ->and($entry->data['headline'] ?? null)->toBe('Big News');
});

it('edits an entry through the row action', function (): void {
    $entry = ContentEntry::factory()->create(['title' => 'Original']);

    Livewire::test(ListContentEntries::class)
        ->callTableAction('edit', $entry, ['title' => 'Revised']);

    expect($entry->fresh()->title)->toBe('Revised');
});

it('deletes an entry through the row action', function (): void {
    $entry = ContentEntry::factory()->create();

    Livewire::test(ListContentEntries::class)->callTableAction('delete', $entry);

    $this->assertModelMissing($entry);
});

it('scopes entries to the current tenant', function (): void {
    Filament::setTenant($this->team);
    $mine = ContentEntry::factory()->create();

    $otherTeam = Team::factory()->create(['user_id' => $this->user->id]);
    Filament::setTenant($otherTeam);
    $theirs = ContentEntry::factory()->create();

    Filament::setTenant($this->team);

    expect($mine->team_id)->toBe($this->team->id)
        ->and($theirs->team_id)->toBe($otherTeam->id);

    Livewire::test(ListContentEntries::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
