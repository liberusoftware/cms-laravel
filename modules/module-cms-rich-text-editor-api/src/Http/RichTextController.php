<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\RichTextEditor\Services\RichTextService;

final class RichTextController
{
    public function sanitize(Request $request, RichTextService $service): JsonResponse
    {
        $html = $request->validate(['html' => ['required', 'string']])['html'];

        return response()->json(['data' => ['html' => $service->sanitize($html)]]);
    }

    public function prepare(Request $request, RichTextService $service): JsonResponse
    {
        $data = $request->validate(['html' => ['required', 'string'], 'format' => ['sometimes', 'string', 'in:html,markdown,plain']]);

        return response()->json(['data' => $service->prepare($data['html'], $data['format'] ?? 'html')]);
    }

    public function embed(Request $request, RichTextService $service): JsonResponse
    {
        $data = $request->validate(['url' => ['required', 'url'], 'title' => ['nullable', 'string', 'max:255']]);

        return response()->json(['data' => ['html' => $service->embed($data['url'], $data['title'] ?? null)]]);
    }
}
