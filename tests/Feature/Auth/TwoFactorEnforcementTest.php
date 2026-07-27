<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTwoFactorForPrivilegedUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('app'));
});

function passThrough(User $user): Response
{
    $request = Request::create('/app', 'GET');
    $request->setUserResolver(fn (): User => $user);

    return app(EnsureTwoFactorForPrivilegedUsers::class)
        ->handle($request, fn (): Response => new Response('ok'));
}

it('redirects a privileged user without 2FA to the setup page', function (): void {
    $user = User::factory()->create();
    $user->createPersonalTeam(); // assigns the team's super_admin role

    $response = passThrough($user);

    expect($response->isRedirect())->toBeTrue()
        ->and($response->headers->get('Location'))->toContain('two-factor-setup');
});

it('lets a privileged user with 2FA enabled through', function (): void {
    $user = User::factory()->create();
    $user->createPersonalTeam();
    $user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_confirmed_at' => now(),
    ])->save();

    expect(passThrough($user->fresh())->getContent())->toBe('ok');
});

it('does not affect a non-privileged user', function (): void {
    $user = User::factory()->create();

    expect(passThrough($user)->getContent())->toBe('ok');
});

it('does nothing when enforcement is disabled', function (): void {
    config()->set('two-factor.enforce', false);

    $user = User::factory()->create();
    $user->createPersonalTeam();

    expect(passThrough($user)->getContent())->toBe('ok');
});
