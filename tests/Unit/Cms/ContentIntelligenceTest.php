<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentIntelligence\Services\ContentIntelligenceService;

uses(RefreshDatabase::class);

it('stores quality and improvement insights with rationale and score', function (): void {
    $insight = app(ContentIntelligenceService::class)->analyze(['subject_type' => 'page', 'subject_key' => '42', 'metric' => 'readability', 'score' => 78.5, 'summary' => 'Simplify two paragraphs', 'rationale' => 'Long sentences reduce clarity'], 3);

    expect($insight->team_id)->toBe(3)
        ->and($insight->score)->toBe(78.5)
        ->and($insight->status)->toBe('open');
});

it('validates scores and supports review queue transitions', function (): void {
    $service = app(ContentIntelligenceService::class);

    expect(fn () => $service->analyze(['subject_type' => 'page', 'subject_key' => '42', 'metric' => 'seo', 'score' => 120, 'summary' => 'Invalid'], 3))
        ->toThrow(ValidationException::class);

    $insight = $service->analyze(['subject_type' => 'page', 'subject_key' => '42', 'metric' => 'seo', 'summary' => 'Add a description'], 3);
    expect($service->review($insight, 'accepted')->status)->toBe('accepted');
});

it('does not allow analysis input to bypass the review lifecycle', function (): void {
    $service = app(ContentIntelligenceService::class);
    $insight = $service->analyze([
        'subject_type' => 'page',
        'subject_key' => '42',
        'metric' => 'seo',
        'summary' => 'Add a description',
        'status' => 'accepted',
    ], 3);

    expect($insight->status)->toBe('open');
    $accepted = $service->review($insight, 'accepted');

    expect(fn () => $service->review($accepted, 'dismissed'))
        ->toThrow(ValidationException::class);
});
