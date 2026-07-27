<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function registrationInput(string $email, string $password): array
{
    return [
        'name' => 'New User',
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
        'terms' => 'yes',
    ];
}

it('rejects a registration password found in a breach', function (): void {
    // Report every password as compromised.
    app()->bind(UncompromisedVerifier::class, fn (): UncompromisedVerifier => new class implements UncompromisedVerifier
    {
        public function verify($data): bool
        {
            return false;
        }
    });

    expect(fn () => app(CreateNewUser::class)->create(registrationInput('breached@example.com', 'password-in-a-breach')))
        ->toThrow(ValidationException::class);
});

it('accepts an uncompromised registration password', function (): void {
    // The base TestCase binds an "always uncompromised" verifier.
    $user = app(CreateNewUser::class)->create(registrationInput('fresh@example.com', 'a-strong-unbroken-passphrase'));

    expect($user->email)->toBe('fresh@example.com');
});
