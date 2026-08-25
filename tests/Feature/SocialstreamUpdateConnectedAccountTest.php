<?php

use App\Actions\Socialstream\UpdateConnectedAccount;
use App\Models\ConnectedAccount;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use JoelButcher\Socialstream\RefreshedCredentials;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Socialite\Two\User as ProviderUser;

uses(RefreshDatabase::class);

test('an account owner can update connected provider credentials', function (): void {
    $owner = User::factory()->create();
    $account = ConnectedAccount::factory()->for($owner)->create();
    $providerUser = ProviderUser::fake([
        'id' => 'provider-123',
        'name' => 'Provider Name',
        'nickname' => 'provider-user',
        'email' => 'provider@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
        'token' => 'new-token',
        'refreshToken' => 'new-refresh-token',
        'expiresIn' => 3600,
    ]);

    $updated = app(UpdateConnectedAccount::class)->update($owner, $account, 'GitHub', $providerUser);

    expect($updated->refresh()->only([
        'provider', 'provider_id', 'name', 'nickname', 'email', 'avatar_path',
        'token', 'secret', 'refresh_token',
    ]))->toMatchArray([
        'provider' => 'github',
        'provider_id' => 'provider-123',
        'name' => 'Provider Name',
        'nickname' => 'provider-user',
        'email' => 'provider@example.com',
        'avatar_path' => 'https://example.com/avatar.jpg',
        'token' => 'new-token',
        'secret' => null,
        'refresh_token' => 'new-refresh-token',
    ]);
    expect($updated->expires_at)->toBeInstanceOf(Carbon::class);
});

test('a user cannot update another users connected account', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $account = ConnectedAccount::factory()->for($owner)->create();

    expect(fn () => app(UpdateConnectedAccount::class)->update(
        $otherUser,
        $account,
        'github',
        ProviderUser::fake(),
    ))->toThrow(AuthorizationException::class);
});

test('a connected account can refresh its credentials', function (): void {
    $account = ConnectedAccount::factory()->for(User::factory()->create())->create([
        'provider' => 'github',
    ]);
    $expiry = now()->addHour();

    Socialstream::refreshesTokensForProviderUsing('github', fn (): RefreshedCredentials => new RefreshedCredentials(
        'refreshed-token',
        'refreshed-secret',
        'refreshed-refresh-token',
        $expiry,
    ));

    try {
        $updated = app(UpdateConnectedAccount::class)->updateRefreshToken($account);

        expect($updated->refresh()->only(['token', 'secret', 'refresh_token']))->toMatchArray([
            'token' => 'refreshed-token',
            'secret' => 'refreshed-secret',
            'refresh_token' => 'refreshed-refresh-token',
        ]);
        expect($updated->expires_at->timestamp)->toBe($expiry->timestamp);
    } finally {
        Socialstream::$refreshTokenResolvers = [];
    }
});
