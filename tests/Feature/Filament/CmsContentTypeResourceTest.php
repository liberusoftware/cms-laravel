<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentEntitiesFilament\Resources\Pages\ListContentBundles as ListContentTypes;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['content-entries.view', 'content-entries.create', 'content-entries.update', 'content-entries.delete']);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->team);
});

it('renders the content types list', function (): void {
    Livewire::test(ListContentTypes::class)->assertSuccessful();
});

it('lists content type records', function (): void {
    $types = ContentType::factory()->count(3)->create();

    Livewire::test(ListContentTypes::class)->assertCanSeeTableRecords($types);
});

it('creates a content bundle', function (): void {
    Livewire::test(ListContentTypes::class)
        ->callAction('create', [
            'key' => 'portfolio_item',
            'name' => 'Portfolio Item',
            'singular_label' => 'Portfolio Item',
            'plural_label' => 'Portfolio Items',
        ]);

    $type = ContentType::query()->where('key', 'portfolio_item')->first();

    expect($type)->not->toBeNull()
        ->and($type->name)->toBe('Portfolio Item')
        ->and($type->plural_label)->toBe('Portfolio Items');
});

it('edits a content type through the row action', function (): void {
    $type = ContentType::factory()->create(['name' => 'Old Name']);

    Livewire::test(ListContentTypes::class)
        ->callTableAction('edit', $type, ['name' => 'New Name']);

    expect($type->fresh()->name)->toBe('New Name');
});

it('deletes a content type through the row action', function (): void {
    $type = ContentType::factory()->create();

    Livewire::test(ListContentTypes::class)->callTableAction('delete', $type);

    $this->assertModelMissing($type);
});
