<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Forms\Filament\FormResource;
use Liberu\Cms\Forms\Filament\Pages\ListForms;
use Liberu\Cms\Forms\Models\Form;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    FormResource::registerTenancyModelGlobalScope($panel);
    FormResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

it('renders the forms list', function (): void {
    Livewire::test(ListForms::class)->assertSuccessful();
});

it('lists form records for the tenant', function (): void {
    $forms = Form::factory()->count(3)->create(['team_id' => $this->team->id]);

    Livewire::test(ListForms::class)->assertCanSeeTableRecords($forms);
});

it('creates a form through the modal and generates a slug', function (): void {
    Livewire::test(ListForms::class)
        ->callAction('create', ['name' => 'Contact Us']);

    $this->assertDatabaseHas('cms_forms', ['name' => 'Contact Us', 'slug' => 'contact-us', 'team_id' => $this->team->id]);
});
