<?php

use App\Models\Team;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

it('creates a personal team and assigns the current tenant', function (): void {
    $user = User::factory()->create(['name' => 'Ada Lovelace']);

    $team = $user->createPersonalTeam();

    expect($team->name)->toBe("Ada's Team")
        ->and($team->personal_team)->toBeTrue()
        ->and($user->fresh()->current_team_id)->toBe($team->id)
        ->and($user->fresh()->personalTeam()->is($team))->toBeTrue();
});

it('switches only to teams the user owns or belongs to', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();
    $owned = Team::factory()->for($owner, 'owner')->create();
    $joined = Team::factory()->for($member, 'owner')->create();
    $joined->users()->attach($owner);
    $foreign = Team::factory()->for($outsider, 'owner')->create();

    expect($owner->switchTeam($joined))->toBeTrue()
        ->and($owner->fresh()->current_team_id)->toBe($joined->id)
        ->and($owner->switchTeam($foreign))->toBeFalse()
        ->and($owner->belongsToTeam($owned))->toBeTrue()
        ->and($owner->ownsTeam($owned))->toBeTrue();
});

it('lists owned and joined teams in name order', function (): void {
    $user = User::factory()->create();
    $owned = Team::factory()->for($user, 'owner')->create(['name' => 'Zed']);
    $joined = Team::factory()->create(['name' => 'Alpha']);
    $joined->users()->attach($user);

    expect($user->allTeams())->toBeInstanceOf(Collection::class)
        ->pluck('name')->all()->toBe(['Alpha', 'Zed']);
});

it('resolves a missing current team from the personal team', function (): void {
    $user = User::factory()->create();
    $team = $user->createPersonalTeam();
    $user->forceFill(['current_team_id' => null])->save();
    $user->unsetRelation('currentTeam');

    expect($user->currentTeam()->get()->first()->is($team))->toBeTrue();
});

it('checks panel tenant access through team membership', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user, 'owner')->create();
    $other = Team::factory()->create();

    expect($user->canAccessTenant($team))->toBeTrue()
        ->and($user->canAccessTenant($other))->toBeFalse()
        ->and($user->getTenants(Panel::make()))->toBeInstanceOf(Collection::class);
});
