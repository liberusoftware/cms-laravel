<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Seo\SeoMetadataService;

final class SeoController
{
    public function show(Request $request, string $type, int $id, SeoMetadataService $service): JsonResponse
    {
        return response()->json(['data' => $service->check($type, $id)]);
    }
}
