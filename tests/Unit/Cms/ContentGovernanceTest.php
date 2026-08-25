<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentGovernance\Services\ContentGovernanceService;

uses(RefreshDatabase::class);

it('records ownership, policy labels, classification, and review metadata', function (): void {
    $record = app(ContentGovernanceService::class)->record('page', '42', ['owner_id' => 7, 'steward_id' => 8, 'policy_labels' => ['regulated'], 'classification' => 'confidential', 'review_due_at' => '2026-12-01'], 3);

    expect($record->team_id)->toBe(3)
        ->and($record->classification)->toBe('confidential')
        ->and($record->policy_labels)->toBe(['regulated']);
});

it('enforces legal holds and appends compliance evidence', function (): void {
    $service = app(ContentGovernanceService::class);
    $record = $service->record('page', '42', [], 3);

    expect(fn () => $service->placeLegalHold($record, ''))->toThrow(ValidationException::class);
    $service->placeLegalHold($record, 'Investigation');
    $record = $service->addEvidence($record, ['type' => 'review', 'reference' => 'ticket-42']);

    expect($record->legal_hold)->toBeTrue()
        ->and($record->evidence)->toHaveCount(1);

    expect($service->releaseLegalHold($record)->legal_hold)->toBeFalse();
});
