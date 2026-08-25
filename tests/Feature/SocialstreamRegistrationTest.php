<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use JoelButcher\Socialstream\Providers;
use Laravel\Fortify\Features as FortifyFeatures;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Mockery;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('socialstream config has social media providers', function (): void {
    $providers = config('socialstream.providers');

    expect($providers)->toContain(Providers::bitbucket())
        ->toContain(Providers::facebook())
        ->toContain(Providers::github())
        ->toContain(Providers::gitlab())
        ->toContain(Providers::google())
        ->toContain(Providers::linkedin())
        ->toContain(Providers::linkedinOpenId())
        ->toContain(Providers::slack())
        ->toContain(Providers::twitterOAuth2())
        ->not->toContain(Providers::twitterOAuth1());
});

test('users get redirected correctly', function (string $provider): void {
    if (! Providers::enabled($provider)) {
        $this->markTestSkipped("Registration support with the $provider provider is not enabled.");
    }

    config()->set("services.$provider", [
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'redirect' => "http://localhost/oauth/$provider/callback",
    ]);

    $response = get("/oauth/$provider");
    $response->assertRedirectContains($provider);
})->with([
    [Providers::bitbucket()],
    [Providers::facebook()],
    [Providers::github()],
    [Providers::gitlab()],
    [Providers::google()],
    [Providers::linkedin()],
    [Providers::linkedinOpenId()],
    [Providers::slack()],
    [Providers::twitterOAuth2()],
]);

test('users can register using socialite providers', function (string $socialiteProvider): void {
    if (! FortifyFeatures::enabled(FortifyFeatures::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    if (! Providers::enabled($socialiteProvider)) {
        $this->markTestSkipped("Registration support with the $socialiteProvider provider is not enabled.");
    }

    $user = (new User)
        ->map([
            'id' => 'abcdefgh',
            'nickname' => 'Jane',
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'avatar' => null,
            'avatar_original' => null,
        ])
        ->setToken('user-token')
        ->setRefreshToken('refresh-token')
        ->setExpiresIn(3600);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn($user);

    Socialite::shouldReceive('driver')->once()->with($socialiteProvider)->andReturn($provider);

    Session::put('socialstream.previous_url', route('register'));

    $response = get("/oauth/$socialiteProvider/callback");

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
})->with([
    [Providers::bitbucket()],
    [Providers::facebook()],
    [Providers::github()],
    [Providers::gitlab()],
    [Providers::google()],
    [Providers::linkedin()],
    [Providers::linkedinOpenId()],
    [Providers::slack()],
    [Providers::twitterOAuth2()],
]);
