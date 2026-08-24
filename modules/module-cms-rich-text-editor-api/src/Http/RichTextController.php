<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\RichTextEditor\Services\RichTextService;

final class RichTextController
{
    public function prepare(Request $request, RichTextService $service): JsonResponse
    {
        $data = $request->validate(['html' => ['required', 'string'], 'format' => ['sometimes', 'string', 'in:html,markdown,plain']]);

        return response()->json(['data' => $service->prepare($data['html'], $data['format'] ?? 'html')]);
    }
}
