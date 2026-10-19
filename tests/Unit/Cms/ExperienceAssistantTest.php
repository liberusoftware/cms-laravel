<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ExperienceAssistant\Services\ExperienceAssistantService;

uses(RefreshDatabase::class);

it('checks and approves tenant-scoped experience suggestions', function (): void {
    $service = app(ExperienceAssistantService::class);
    $suggestion = $service->suggest('landing-page', ['blocks' => ['hero'], 'mobile' => ['hero']], ['max_blocks' => 3], 7);
    $approved = $service->approve($suggestion, 'designer-1', 7);

    expect($approved->status)->toBe('approved')->and($approved->diagnostics['errors'])->toBe([]);
});

it('blocks inaccessible designs and cross-tenant approval', function (): void {
    $service = app(ExperienceAssistantService::class);
    $suggestion = $service->suggest('landing-page', ['blocks' => ['hero'], 'contrast_ratio' => 2], [], 7);

    expect(fn () => $service->approve($suggestion, 'designer-1', 7))->toThrow(ValidationException::class)
        ->and(fn () => $service->approve($suggestion, 'designer-1', 8))->toThrow(ValidationException::class);
});
