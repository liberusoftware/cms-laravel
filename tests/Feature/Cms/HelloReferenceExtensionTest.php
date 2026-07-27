<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Block\BlockRendererInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Hello\Filament\GreetingResource;
use Liberu\Cms\Hello\Hooks\GreetingFilter;
use Liberu\Cms\Hello\Models\Greeting;
use Liberu\Cms\Users\Access\SyncPermissions;

uses(RefreshDatabase::class);

it('registers a block that renders a greeting, marked by the consumed core filter', function (): void {
    $html = app(BlockRendererInterface::class)->render([
        'type' => 'greeting',
        'data' => ['name' => 'Ada'],
    ]);

    expect($html)->toContain('cms-block-greeting', 'Hello, Ada!')
        ->and($html)->toContain('greeted by cms-hello');
});

it('applies its own GreetingFilter, so another extension can transform the greeting', function (): void {
    app(HookBusInterface::class)->listen(GreetingFilter::class, function (GreetingFilter $filter): void {
        $filter->message = strtoupper($filter->message);
    });

    expect(app(BlockRendererInterface::class)->render(['type' => 'greeting', 'data' => ['name' => 'Ada']]))
        ->toContain('HELLO, ADA!');
});

it('registers a custom greeting field kind', function (): void {
    $registry = app(FieldTypeRegistryInterface::class);

    expect($registry->has('greeting'))->toBeTrue()
        ->and($registry->options())->toHaveKey('greeting', 'Greeting');
});

it('serves greetings through the registered Delivery API endpoint', function (): void {
    Greeting::create(['name' => 'Ada', 'message' => 'Hi Ada']);

    Sanctum::actingAs(Team::factory()->create(), ['content:read'], 'sanctum');

    $this->getJson('/api/v1/greetings')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Ada', 'message' => 'Hi Ada']);
});

it('registers the greetings resource into the admin registry', function (): void {
    expect(app(AdminResourceRegistryInterface::class)->resources()['hello'] ?? [])
        ->toContain(GreetingResource::class);
});

it('gates the greetings admin resource by the hello permission', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($team);
    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    expect(GreetingResource::canViewAny())->toBeFalse();

    grantCmsPermissions($user, $team, ['hello.view']);

    expect(GreetingResource::canViewAny())->toBeTrue();
});
