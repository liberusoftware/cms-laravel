<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Filament\Pages\ListContentTypes;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['content-types.view', 'content-types.create', 'content-types.update', 'content-types.delete']);
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

it('creates a content type with a field schema', function (): void {
    Livewire::test(ListContentTypes::class)
        ->callAction('create', [
            'key' => 'portfolio_item',
            'name' => 'Portfolio Item',
            'singular_label' => 'Portfolio Item',
            'plural_label' => 'Portfolio Items',
            'fields' => [
                ['name' => 'headline', 'label' => 'Headline', 'type' => 'text', 'required' => true, 'options' => []],
            ],
        ]);

    $type = ContentType::query()->where('key', 'portfolio_item')->first();

    expect($type)->not->toBeNull()
        ->and($type->fields)->toHaveCount(1)
        ->and($type->fields[0]['name'])->toBe('headline');
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
