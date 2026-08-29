<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\CommentsAndDiscussion\Models\Comment;
use Liberu\Cms\CommentsAndDiscussion\Models\CommentReport;
use Liberu\Cms\CommentsAndDiscussion\Models\CommentSubscription;

final readonly class CommentService
{
    public function list(string $type, string $id, ?int $teamId = null, int $perPage = 25): LengthAwarePaginator
    {
        return Comment::query()->where('commentable_type', $type)->where('commentable_id', $id)->where('status', 'approved')->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->with('children')->latest()->paginate(max(1, min($perPage, (int) config('comments-and-discussion.pagination.max', 100))));
    }

    public function create(array $data, ?int $authorId = null, ?int $teamId = null): Comment
    {
        if (blank($data['commentable_type'] ?? null) || blank($data['commentable_id'] ?? null)) {
            throw ValidationException::withMessages(['commentable' => 'A discussion subject type and identifier are required.']);
        }
        $body = trim((string) ($data['body'] ?? ''));
        if ($body === '' || mb_strlen($body) > 10000) {
            throw ValidationException::withMessages(['body' => 'Comment body must contain 1 to 10000 characters.']);
        }
        if ($authorId === null && (trim((string) ($data['guest_name'] ?? '')) === '' || ! filter_var($data['guest_email'] ?? null, FILTER_VALIDATE_EMAIL))) {
            throw ValidationException::withMessages(['guest_email' => 'A guest name and valid email are required.']);
        }
        $parent = isset($data['parent_id']) ? Comment::query()->where('team_id', $teamId)->findOrFail($data['parent_id']) : null;
        if ($parent && ($parent->commentable_type !== $data['commentable_type'] || (string) $parent->commentable_id !== (string) $data['commentable_id'] || $parent->team_id !== $teamId)) {
            throw ValidationException::withMessages(['parent_id' => 'The parent comment is outside this discussion.']);
        }

        return Comment::query()->create(['team_id' => $teamId, 'commentable_type' => (string) ($data['commentable_type'] ?? ''), 'commentable_id' => (string) ($data['commentable_id'] ?? ''), 'parent_id' => $parent?->getKey(), 'author_id' => $authorId, 'guest_name' => $authorId === null ? trim((string) $data['guest_name']) : null, 'guest_email' => $authorId === null ? strtolower(trim((string) $data['guest_email'])) : null, 'body' => $body, 'status' => config('comments-and-discussion.moderation.required', true) ? 'pending' : 'approved']);
    }

    public function update(Comment $comment, string $body, ?int $actorId = null, bool $moderator = false): Comment
    {
        if (! $moderator && ($actorId === null || (int) $comment->author_id !== $actorId || ($comment->created_at?->addMinutes((int) config('comments-and-discussion.editing_window_minutes', 15))->isPast() ?? true))) {
            throw ValidationException::withMessages(['comment' => 'This comment cannot be edited.']);
        }
        $body = trim($body);
        if ($body === '' || mb_strlen($body) > 10000) {
            throw ValidationException::withMessages(['body' => 'Comment body must contain 1 to 10000 characters.']);
        }
        $comment->update(['body' => $body, 'edited_at' => now()]);

        return $comment->fresh();
    }

    public function moderate(Comment $comment, string $status): Comment
    {
        if (! in_array($status, ['approved', 'rejected', 'spam', 'removed'], true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported moderation status.']);
        }
        if ($comment->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Only pending comments can be moderated.']);
        }
        $comment->update(['status' => $status, 'moderated_at' => now()]);

        return $comment->fresh();
    }

    public function report(Comment $comment, string $reason, ?int $reporterId = null): CommentReport
    {
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 120) {
            throw ValidationException::withMessages(['reason' => 'A report reason is required.']);
        }

        return CommentReport::query()->firstOrCreate(['comment_id' => $comment->getKey(), 'reporter_id' => $reporterId, 'reason' => $reason], ['status' => 'open']);
    }

    public function subscribe(Comment $comment, int $subscriberId): CommentSubscription
    {
        return CommentSubscription::query()->firstOrCreate(['comment_id' => $comment->getKey(), 'subscriber_id' => $subscriberId]);
    }

    public function prune(int $retentionDays = 0): int
    {
        $days = $retentionDays > 0 ? $retentionDays : (int) config('comments-and-discussion.retention_days', 3650);

        return Comment::query()->whereIn('status', ['removed', 'spam'])->where('updated_at', '<', now()->subDays($days))->delete();
    }
}
