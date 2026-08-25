<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\TranslationAssistant\Queries\TranslationAssistantQuery;
use Liberu\Cms\TranslationAssistant\Services\TranslationAssistantService;

uses(RefreshDatabase::class);

it('enforces glossary and style rules before review', function (): void {
    $service = app(TranslationAssistantService::class);
    $service->addGlossary('en', 'fr', 'checkout', 'paiement', ['payfast']);
    $service->addStyleRule('fr', 'No exclamation marks', '/!/', 'Avoid exclamation marks.');

    $draft = $service->draft('content-entry', 1, 'en', 'fr', 'Checkout now', 'Payfast!', .8, 'test-provider', 'test-model', ['request_id' => 'req-1']);

    expect($draft->violations)->toHaveCount(3)
        ->and($draft->provenance['request_id'])->toBe('req-1');

    expect(fn () => $service->review($draft, 'approved', 'user', 1))->toThrow(ValidationException::class);
});

it('queries assistant drafts through the module boundary', function (): void {
    $service = app(TranslationAssistantService::class);
    $draft = $service->draft('page', 2, 'en', 'de', 'Hello', 'Hallo', .95, 'provider', 'model');

    expect(app(TranslationAssistantQuery::class)->draft($draft->id))->not->toBeNull();
});
