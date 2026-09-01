<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can view the registration page', function (): void {
    $this->get('/register')->assertSuccessful();
});

it('can register with valid data', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'name' => 'Test User']);
    expect(session('account_setup_required'))->toBeTrue();
    expect(User::where('email', 'test@example.com')->first()->setup_completed_at)->toBeNull();
});

it('fails registration when name is missing', function (): void {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

it('fails registration when email is missing', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('fails registration when email is invalid', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('fails registration when email is already taken', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('fails registration when password is missing', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('fails registration when password confirmation does not match', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('fails registration when password is too short', function (): void {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});
