<?php

declare(strict_types=1);

use App\Filament\Pages\AccountSetupWizard;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the guided account, workspace, and integration setup page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($team);

    Livewire::test(AccountSetupWizard::class)
        ->assertSuccessful()
        ->assertSee('Finish setting up your workspace')
        ->assertSee('Integrations');
});
