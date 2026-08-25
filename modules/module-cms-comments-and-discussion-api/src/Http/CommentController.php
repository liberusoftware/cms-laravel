<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\CommentsAndDiscussion\Models\Comment;
use Liberu\Cms\CommentsAndDiscussion\Services\CommentService;

final class CommentController
{
    public function index(Request $request, string $type, string $id, CommentService $service): JsonResponse
    {
        return response()->json(['data' => $service->list($type, $id, $request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }

    public function show(Comment $comment): JsonResponse
    {
        abort_unless($comment->status === 'approved' || auth()->user()?->can('comments-and-discussion.view'), 404);

        return response()->json(['data' => $comment->load('children')]);
    }

    public function store(Request $request, CommentService $service): JsonResponse
    {
        $data = $request->validate(['commentable_type' => ['required', 'string', 'max:120'], 'commentable_id' => ['required', 'string', 'max:120'], 'parent_id' => ['nullable', 'integer'], 'body' => ['required', 'string', 'max:10000'], 'guest_name' => ['nullable', 'string', 'max:160'], 'guest_email' => ['nullable', 'email', 'max:255']]);

        return response()->json(['data' => $service->create($data, $request->user()?->getAuthIdentifier(), $request->user()?->current_team_id)], 201);
    }

    public function update(Request $request, Comment $comment, CommentService $service): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);

        return response()->json(['data' => $service->update($comment, $data['body'], $request->user()?->getAuthIdentifier(), $request->user()?->can('comments-and-discussion.moderate') ?? false)]);
    }

    public function moderate(Request $request, Comment $comment, CommentService $service): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected,spam,removed']]);

        return response()->json(['data' => $service->moderate($comment, $data['status'])]);
    }

    public function report(Request $request, Comment $comment, CommentService $service): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:120']]);

        return response()->json(['data' => $service->report($comment, $data['reason'], $request->user()?->getAuthIdentifier())], 201);
    }

    public function subscribe(Request $request, Comment $comment, CommentService $service): JsonResponse
    {
        abort_unless($request->user(), 401);

        return response()->json(['data' => $service->subscribe($comment, (int) $request->user()->getAuthIdentifier())], 201);
    }
}
