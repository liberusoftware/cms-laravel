<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Filament\ContentEntryResource;
use Liberu\Cms\ContentTypes\Filament\ContentTypeResource;
use Liberu\Cms\Forms\Filament\FormResource;
use Liberu\Cms\Forms\Filament\FormSubmissionResource;
use Liberu\Cms\Media\Filament\MediaResource;
use Liberu\Cms\Menus\Filament\MenuItemResource;
use Liberu\Cms\Menus\Filament\MenuResource;
use Liberu\Cms\Notifications\Filament\NotificationLogResource;
use Liberu\Cms\Pages\Filament\PageResource;
use Liberu\Cms\Posts\Filament\PostResource;
use Liberu\Cms\Users\Access\SyncPermissions;

uses(RefreshDatabase::class);

/**
 * Sign in a fresh user with a personal team, materialise every CMS permission,
 * and return the team so a test can grant a precise subset. The user starts
 * holding no permissions.
 */
function signInWithoutCmsPermissions(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    test()->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($team);

    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    return [$user, $team];
}

dataset('cms resources', [
    'pages' => [PageResource::class, 'pages'],
    'posts' => [PostResource::class, 'posts'],
    'media' => [MediaResource::class, 'media'],
    'content types' => [ContentTypeResource::class, 'content-types'],
    'content entries' => [ContentEntryResource::class, 'content-entries'],
    'menus' => [MenuResource::class, 'menus'],
    'menu items' => [MenuItemResource::class, 'menus'],
    'forms' => [FormResource::class, 'forms'],
    'form submissions' => [FormSubmissionResource::class, 'form-submissions'],
    'notification logs' => [NotificationLogResource::class, 'notification-logs'],
]);

it('forbids every action to a user without permissions', function (string $resource, string $key): void {
    signInWithoutCmsPermissions();

    $model = $resource::getModel();
    $record = new $model;

    expect($resource::canViewAny())->toBeFalse()
        ->and($resource::canCreate())->toBeFalse()
        ->and($resource::canEdit($record))->toBeFalse()
        ->and($resource::canDelete($record))->toBeFalse();
})->with('cms resources');

it('grants viewing but still forbids deletion to a view-only user', function (string $resource, string $key): void {
    [$user, $team] = signInWithoutCmsPermissions();
    grantCmsPermissions($user, $team, ["{$key}.view"]);

    $model = $resource::getModel();
    $record = new $model;

    expect($resource::canViewAny())->toBeTrue()
        ->and($resource::canDelete($record))->toBeFalse();
})->with('cms resources');
