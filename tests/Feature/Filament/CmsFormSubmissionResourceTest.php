<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Forms\Filament\FormSubmissionResource;
use Liberu\Cms\Forms\Filament\Pages\ListFormSubmissions;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Forms\Models\FormSubmission;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['form-submissions.view', 'form-submissions.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    FormSubmissionResource::registerTenancyModelGlobalScope($panel);
    FormSubmissionResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

it('renders the submissions list', function (): void {
    Livewire::test(ListFormSubmissions::class)->assertSuccessful();
});

it('lists submissions for the tenant', function (): void {
    $form = Form::factory()->create(['team_id' => $this->team->id]);
    $submission = FormSubmission::factory()->create(['form_id' => $form->id, 'team_id' => $this->team->id]);

    Livewire::test(ListFormSubmissions::class)->assertCanSeeTableRecords([$submission]);
});

it('cannot create submissions from the admin', function (): void {
    expect(FormSubmissionResource::canCreate())->toBeFalse();
});
