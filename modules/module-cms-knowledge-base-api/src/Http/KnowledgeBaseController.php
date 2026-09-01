<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\KnowledgeBase\Queries\KnowledgeBaseQuery;
use Liberu\Cms\KnowledgeBase\Services\KnowledgeBaseService;
use Liberu\Cms\KnowledgeBaseApi\Http\Resources\KnowledgeArticleResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class KnowledgeBaseController
{
    public function index(Request $request, KnowledgeBaseQuery $query): JsonResponse
    {
        $raw = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($raw) ? $raw : [];
        $perPage = is_int($data['per_page'] ?? null) ? $data['per_page'] : 15;
        $search = is_string($data['search'] ?? null) ? $data['search'] : '';
        $articles = $query->paginate($perPage, $search);

        return response()->json(['data' => KnowledgeArticleResource::collection($articles->getCollection()), 'meta' => ['current_page' => $articles->currentPage(), 'last_page' => $articles->lastPage(), 'per_page' => $articles->perPage(), 'total' => $articles->total()]]);
    }

    public function show(string $key, KnowledgeBaseQuery $query): KnowledgeArticleResource
    {
        $article = $query->find($key);
        if (! $article) {
            throw new NotFoundHttpException;
        }

        return new KnowledgeArticleResource($article);
    }

    public function store(Request $request, KnowledgeBaseService $service): KnowledgeArticleResource
    {
        $raw = $request->validate(['slug' => ['required', 'string', 'max:180'], 'title' => ['required', 'string', 'max:240'], 'body' => ['required', 'string'], 'parent_id' => ['nullable', 'integer']]);
        if (! is_array($raw)) {
            throw ValidationException::withMessages(['body' => 'The request payload is invalid.']);
        }
        $slug = $raw['slug'] ?? null;
        $title = $raw['title'] ?? null;
        $body = $raw['body'] ?? null;
        $parentId = $raw['parent_id'] ?? null;
        if (! is_string($slug) || ! is_string($title) || ! is_string($body) || ($parentId !== null && ! is_int($parentId))) {
            throw ValidationException::withMessages(['body' => 'The request payload is invalid.']);
        }

        return new KnowledgeArticleResource($service->create($slug, $title, $body, $request->user()?->current_team_id, $parentId));
    }

    public function publish(string $key, Request $request, KnowledgeBaseQuery $query, KnowledgeBaseService $service): KnowledgeArticleResource
    {
        $article = $query->find($key, false);
        if (! $article) {
            throw new NotFoundHttpException;
        }

        return new KnowledgeArticleResource($service->publish($article, $request->user()?->current_team_id));
    }
}
