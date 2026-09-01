<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\EditorialContent\Queries\EditorialContentQuery;
use Liberu\Cms\EditorialContent\Services\EditorialContentService;
use Liberu\Cms\EditorialContentApi\Http\Resources\EditorialPostResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EditorialContentController
{
    public function index(Request $request, EditorialContentQuery $query): JsonResponse
    {
        $raw = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'include_archived' => ['sometimes', 'boolean']]);
        $data = [];
        if (is_array($raw)) {
            foreach ($raw as $key => $value) {
                if (is_string($key)) {
                    $data[$key] = $value;
                }
            }
        }
        $perPage = is_int($data['per_page'] ?? null) ? $data['per_page'] : 15;
        $search = is_string($data['search'] ?? null) ? $data['search'] : '';
        $includeArchived = is_bool($data['include_archived'] ?? null) && $data['include_archived'];
        $posts = $query->paginate($perPage, $search, $includeArchived);

        return response()->json(['data' => EditorialPostResource::collection($posts->getCollection()), 'meta' => ['current_page' => $posts->currentPage(), 'last_page' => $posts->lastPage(), 'per_page' => $posts->perPage(), 'total' => $posts->total()]]);
    }

    public function show(string $key, EditorialContentQuery $query): EditorialPostResource
    {
        $post = $query->find($key, true);
        if (! $post) {
            throw new NotFoundHttpException;
        }

        return new EditorialPostResource($post);
    }

    public function store(Request $request, EditorialContentService $service): EditorialPostResource
    {
        $raw = $request->validate(['slug' => ['required', 'string', 'max:200'], 'title' => ['required', 'string', 'max:240'], 'excerpt' => ['nullable', 'string'], 'body' => ['nullable', 'string'], 'status' => ['sometimes', 'in:draft,published,archived']]);
        $data = [];
        if (is_array($raw)) {
            foreach ($raw as $key => $value) {
                if (is_string($key)) {
                    $data[$key] = $value;
                }
            }
        }

        return new EditorialPostResource($service->post($data, $request->user()?->current_team_id));
    }

    public function publish(string $key, EditorialContentQuery $query, EditorialContentService $service): EditorialPostResource
    {
        $post = $query->find($key);
        if (! $post) {
            throw new NotFoundHttpException;
        }

        return new EditorialPostResource($service->publish($post));
    }

    public function archive(string $key, EditorialContentQuery $query, EditorialContentService $service): EditorialPostResource
    {
        $post = $query->find($key);
        if (! $post) {
            throw new NotFoundHttpException;
        }

        return new EditorialPostResource($service->archive($post));
    }
}
