<?php

use Illuminate\Validation\ValidationException;
use Liberu\Cms\FormBuilder\Services\FormBuilderService;

it('validates conditional multi-step schemas and calculates totals', function (): void {
    $service = app(FormBuilderService::class);
    $steps = [['fields' => [['name' => 'email', 'type' => 'email', 'required' => true], ['name' => 'company', 'type' => 'string', 'when' => ['field' => 'account_type', 'equals' => 'business']]]]];

    expect($service->validate($steps, ['email' => 'ada@example.test', 'account_type' => 'business', 'company' => 'Analytical Engines']))->toMatchArray(['email' => 'ada@example.test'])
        ->and($service->calculate(['total' => ['sum' => ['one', 'two']]], ['one' => 2, 'two' => 3]))->toBe(['total' => 5]);
    expect(fn () => $service->validate($steps, ['email' => 'not-an-email']))->toThrow(ValidationException::class);
});

it('validates confirmations and produces a safe embed', function (): void {
    $service = app(FormBuilderService::class);
    $confirmation = $service->confirmation(['message' => 'Thanks', 'redirect' => 'https://example.test/thanks']);
    $embed = $service->embed('123e4567-e89b-12d3-a456-426614174000', 'https://forms.example.test');

    expect($confirmation['message'])->toBe('Thanks')->and($embed)->toContain('https://forms.example.test/forms/');
    expect(fn () => $service->confirmation(['redirect' => 'javascript:alert(1)']))->toThrow(ValidationException::class);
    expect(fn () => $service->embed('invalid', 'https://forms.example.test'))->toThrow(ValidationException::class);
});
