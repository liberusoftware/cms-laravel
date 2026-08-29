<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\CommentsAndDiscussion\Models\Comment;
use Liberu\Cms\CommentsAndDiscussion\Services\CommentService;

uses(RefreshDatabase::class);

it('creates moderated comments and exposes only approved discussions', function (): void {
    $service = app(CommentService::class);
    $comment = $service->create(['commentable_type' => 'page', 'commentable_id' => '42', 'body' => 'Needs review'], 7, 3);

    expect($comment->status)->toBe('pending')
        ->and($service->list('page', '42', 3)->total())->toBe(0);

    $service->moderate($comment, 'approved');

    expect($service->list('page', '42', 3)->total())->toBe(1);

    expect(fn () => $service->moderate($comment->fresh(), 'rejected'))
        ->toThrow(ValidationException::class);
});

it('rejects cross-discussion parents and invalid guests', function (): void {
    $service = app(CommentService::class);
    $parent = Comment::query()->create(['commentable_type' => 'page', 'commentable_id' => '1', 'body' => 'Parent', 'status' => 'approved']);

    expect(fn () => $service->create(['commentable_type' => 'page', 'commentable_id' => '2', 'parent_id' => $parent->id, 'body' => 'Reply'], 7))
        ->toThrow(ValidationException::class);
    expect(fn () => $service->create(['body' => 'Missing subject'], 7, 3))
        ->toThrow(ValidationException::class);
    expect(fn () => $service->create(['commentable_type' => 'page', 'commentable_id' => '2', 'body' => 'Guest']))
        ->toThrow(ValidationException::class);
});

it('supports editing, reports, subscriptions, and retention', function (): void {
    $service = app(CommentService::class);
    $comment = $service->create(['commentable_type' => 'page', 'commentable_id' => '1', 'body' => 'Original'], 7);
    $service->update($comment, 'Updated', 7);
    $report = $service->report($comment, 'spam', 9);
    $sameReport = $service->report($comment, 'spam', 9);
    $subscription = $service->subscribe($comment, 9);

    expect($comment->fresh()->body)->toBe('Updated')
        ->and($sameReport->id)->toBe($report->id)
        ->and($subscription->subscriber_id)->toBe(9);

    DB::table('cms_comments')->where('id', $comment->id)->update(['status' => 'spam', 'updated_at' => now()->subDays(4000)]);
    expect($service->prune(1))->toBe(1);
});
