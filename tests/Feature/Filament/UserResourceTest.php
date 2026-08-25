<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->team);
});

it('can render the list page', function (): void {
    Livewire::test(ListUsers::class)
        ->assertSuccessful();
});

it('can list users in the table', function (): void {
    $users = User::factory()->count(3)->create();

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

it('can search users by name', function (): void {
    $matchingUser = User::factory()->create(['name' => 'UniqueUserName']);
    $otherUser = User::factory()->create(['name' => 'DifferentPerson']);

    Livewire::test(ListUsers::class)
        ->searchTable('UniqueUserName')
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords([$otherUser]);
});

it('can search users by email', function (): void {
    $matchingUser = User::factory()->create(['email' => 'uniqueuser@example.com']);
    $otherUser = User::factory()->create(['email' => 'other@example.com']);

    Livewire::test(ListUsers::class)
        ->searchTable('uniqueuser@example.com')
        ->assertCanSeeTableRecords([$matchingUser])
        ->assertCanNotSeeTableRecords([$otherUser]);
});

it('can delete a user from the list', function (): void {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->callTableBulkAction('delete', [$user]);

    $this->assertModelMissing($user);
});

it('has the correct model', function (): void {
    expect(UserResource::getModel())->toBe(User::class);
});

it('builds the tenant-aware user form', function (): void {
    $schema = UserForm::configure(Schema::make());

    expect($schema->getComponents())->toHaveCount(1);
});
