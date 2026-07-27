<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Database\Seeders\CmsBaselineRolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $user->id]);
    setPermissionsTeamId($this->team->id);
});

it('provisions the baseline roles mapped to the declared CMS permissions', function (): void {
    $this->seed(CmsBaselineRolesSeeder::class);

    foreach (['super_admin', 'admin', 'editor', 'author', 'viewer'] as $roleName) {
        expect(Role::where('name', $roleName)->where('team_id', $this->team->id)->exists())->toBeTrue();
    }

    $admin = Role::where('name', 'admin')->where('team_id', $this->team->id)->first();
    expect($admin->permissions->pluck('name'))->toContain('pages.view', 'pages.delete', 'modules.manage');
});

it('limits the viewer role to view permissions only', function (): void {
    $this->seed(CmsBaselineRolesSeeder::class);

    $viewer = Role::where('name', 'viewer')->where('team_id', $this->team->id)->first();
    $abilities = $viewer->permissions->pluck('name')
        ->map(fn (string $name): string => explode('.', $name, 2)[1] ?? '')
        ->unique()
        ->values();

    expect($abilities->all())->toBe(['view']);
});

it('excludes management and destructive scopes from the editor role', function (): void {
    $this->seed(CmsBaselineRolesSeeder::class);

    $editor = Role::where('name', 'editor')->where('team_id', $this->team->id)->first();
    $names = $editor->permissions->pluck('name');

    expect($names)->toContain('pages.create', 'pages.delete')
        ->and($names)->not->toContain('modules.manage', 'api-tokens.manage');
});
