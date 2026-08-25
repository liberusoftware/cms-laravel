<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentTemplates\Models\ContentTemplate;
use Liberu\Cms\ContentTemplates\Services\ContentTemplatesService;

final class ContentTemplatesController
{
    public function index(Request $request, ContentTemplatesService $service): JsonResponse
    {
        return response()->json(['data' => $service->templates($request->user()?->current_team_id, $request->input('content_type'), $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContentTemplatesService $service): JsonResponse
    {
        return response()->json(['data' => $service->create($request->validate(['name' => ['required', 'string'], 'slug' => ['required', 'string'], 'content_type' => ['required', 'string'], 'version' => ['nullable', 'integer', 'min:1'], 'schema' => ['required', 'array'], 'defaults' => ['array'], 'rollout_percent' => ['nullable', 'integer', 'between:0,100']]), $request->user()?->current_team_id)], 201);
    }

    public function publish(ContentTemplate $template, ContentTemplatesService $service): JsonResponse
    {
        return response()->json(['data' => $service->publish($template)]);
    }

    public function lock(ContentTemplate $template, ContentTemplatesService $service): JsonResponse
    {
        return response()->json(['data' => $service->lock($template)]);
    }
}
