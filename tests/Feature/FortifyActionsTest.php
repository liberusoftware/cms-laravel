<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Socialstream\SetUserPassword;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TestVerifiedUser extends User implements MustVerifyEmail
{
    #[Override]
    protected $table = 'users';

    public static bool $verificationNotificationSent = false;

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return (bool) $this->forceFill(['email_verified_at' => now()])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        self::$verificationNotificationSent = true;
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }
}

uses(RefreshDatabase::class);

it('updates a profile photo through the Fortify action', function (): void {
    Storage::fake('public');
    $user = User::factory()->create(['name' => 'Old Name']);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'New Name',
        'email' => $user->email,
        'photo' => UploadedFile::fake()->image('avatar.jpg'),
    ]);

    expect($user->fresh()->name)->toBe('New Name')
        ->and($user->fresh()->profile_photo_path)->not->toBeNull();
});

it('resets verification and notifies when a verified user changes email', function (): void {
    $user = new TestVerifiedUser;
    $user->forceFill([
        'name' => 'Verified User',
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
        'password' => 'password',
    ])->save();
    TestVerifiedUser::$verificationNotificationSent = false;

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'Updated User',
        'email' => 'changed@example.com',
    ]);

    expect($user->fresh()->email_verified_at)->toBeNull()
        ->and(TestVerifiedUser::$verificationNotificationSent)->toBeTrue();
});

it('sets a valid Socialstream password', function (): void {
    $user = User::factory()->create();

    app(SetUserPassword::class)->set($user, [
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects an invalid Socialstream password payload', function (): void {
    expect(fn () => app(SetUserPassword::class)->set(User::factory()->create(), [
        'password' => 'short',
        'password_confirmation' => 'different',
    ]))->toThrow(ValidationException::class);
});
