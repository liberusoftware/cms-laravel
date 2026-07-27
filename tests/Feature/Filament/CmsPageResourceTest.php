<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Filament\PageResource;
use Liberu\Cms\Pages\Filament\Pages\ListPages;
use Liberu\Cms\Pages\Models\Page;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['pages.view', 'pages.create', 'pages.update', 'pages.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    // Livewire::test() does not run Panel::boot(), so register the tenancy scope
    // and creation observer the way a real request would.
    PageResource::registerTenancyModelGlobalScope($panel);
    PageResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

it('renders the pages list', function (): void {
    Livewire::test(ListPages::class)->assertSuccessful();
});

it('lists page records', function (): void {
    $pages = Page::factory()->count(3)->create(['team_id' => $this->team->id]);

    Livewire::test(ListPages::class)->assertCanSeeTableRecords($pages);
});

it('creates a page through the modal', function (): void {
    Livewire::test(ListPages::class)
        ->callAction('create', [
            'title' => 'A Brand New Page',
            'template' => 'default',
            'status' => 'draft',
        ]);

    $this->assertDatabaseHas('cms_pages', ['title' => 'A Brand New Page']);
});

it('generates a slug when none is given', function (): void {
    Livewire::test(ListPages::class)
        ->callAction('create', [
            'title' => 'Slugless Page',
            'template' => 'default',
            'status' => 'draft',
        ]);

    $this->assertDatabaseHas('cms_pages', ['slug' => 'slugless-page']);
});

it('edits a page through the row action', function (): void {
    $page = Page::factory()->create(['title' => 'Original', 'team_id' => $this->team->id]);

    Livewire::test(ListPages::class)
        ->callTableAction('edit', $page, ['title' => 'Renamed']);

    expect($page->fresh()->title)->toBe('Renamed');
});

it('deletes a page through the row action', function (): void {
    $page = Page::factory()->create(['team_id' => $this->team->id]);

    Livewire::test(ListPages::class)->callTableAction('delete', $page);

    $this->assertModelMissing($page);
});

it('scopes the list to the current tenant', function (): void {
    // The creation observer stamps the active tenant, so switch tenants to place
    // each page in a different team.
    Filament::setTenant($this->team);
    $mine = Page::factory()->create();

    $otherTeam = Team::factory()->create(['user_id' => $this->user->id]);
    Filament::setTenant($otherTeam);
    $theirs = Page::factory()->create();

    Filament::setTenant($this->team);

    expect($mine->team_id)->toBe($this->team->id)
        ->and($theirs->team_id)->toBe($otherTeam->id);

    Livewire::test(ListPages::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
