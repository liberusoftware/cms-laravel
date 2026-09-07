<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EditorialWorkflow\Services\EditorialWorkflowService;

uses(RefreshDatabase::class);

it('configures workflow states, transitions, assignments, review, delegation, and evidence', function (): void {
    $service = app(EditorialWorkflowService::class);
    $workflow = $service->create(['key' => 'editorial', 'name' => 'Editorial', 'initial_state' => 'draft'], 7);
    $service->state($workflow, 'draft', 'Draft');
    $service->state($workflow, 'review', 'Review');
    $service->transition($workflow, 'draft', 'review', 'content.review', true);
    $assignment = $service->assign($workflow, 'page', 'page-1', 'user', 'author-1');
    $delegate = $service->assign($workflow, 'page', 'page-1', 'user', 'author-2', 'assignee', $assignment->id);
    $review = $service->review($workflow, 'page', 'page-1', 'reviewer-1', 'approved', 'Looks good.');
    $evidence = $service->evidence($workflow, 'page', 'page-1', 'approved', 'reviewer-1', ['review_id' => $review->id]);

    expect($workflow->states)->toHaveCount(2)
        ->and($workflow->transitions)->toHaveCount(1)
        ->and($delegate->delegated_from_id)->toBe($assignment->id)
        ->and($evidence->payload)->toBe(['review_id' => $review->id]);
});

it('enforces separation of duties and transition invariants', function (): void {
    $service = app(EditorialWorkflowService::class);
    $workflow = $service->create(['key' => 'editorial', 'name' => 'Editorial'], 7);
    $service->assign($workflow, 'post', 'post-1', 'user', 'author-1');

    expect(fn () => $service->review($workflow, 'post', 'post-1', 'author-1', 'approved'))
        ->toThrow(ValidationException::class);
    expect(fn () => $service->transition($workflow, 'draft', 'draft'))
        ->toThrow(ValidationException::class);
    expect(fn () => $service->review($workflow, 'post', 'post-1', 'reviewer-1', 'invalid'))
        ->toThrow(ValidationException::class);
});
