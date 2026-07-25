<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Forms\Models\Form;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    $this->form = Form::factory()->create(['slug' => 'contact', 'team_id' => $this->team->id]);
});

it('accepts a valid submission, stores it, and dispatches the event', function (): void {
    $received = null;
    app(EventBusInterface::class)->listen(FormSubmitted::class, function (FormSubmitted $event) use (&$received): void {
        $received = $event;
    });

    $response = $this->postJson('/forms/contact', [
        'email' => 'visitor@example.com',
        'message' => 'Hello there',
    ]);

    $response->assertCreated()->assertJsonPath('message', 'Thank you for your submission.');

    $this->assertDatabaseHas('cms_form_submissions', [
        'form_id' => $this->form->id,
        'team_id' => $this->team->id,
    ]);

    expect($received)->toBeInstanceOf(FormSubmitted::class)
        ->and($received->formSlug)->toBe('contact')
        ->and($received->data['email'])->toBe('visitor@example.com');
});

it('rejects a submission missing a required field with 422', function (): void {
    $this->postJson('/forms/contact', ['email' => 'visitor@example.com'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('message');
});

it('rejects an invalid field value with 422', function (): void {
    $this->postJson('/forms/contact', ['email' => 'not-an-email', 'message' => 'hi'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

it('silently drops a submission that trips the honeypot', function (): void {
    $response = $this->postJson('/forms/contact', [
        'email' => 'bot@example.com',
        'message' => 'spam',
        '_hp' => 'i am a bot',
    ]);

    $response->assertCreated();
    $this->assertDatabaseCount('cms_form_submissions', 0);
});

it('returns 404 for an unknown form', function (): void {
    $this->postJson('/forms/nope', ['email' => 'a@b.com', 'message' => 'hi'])->assertNotFound();
});
