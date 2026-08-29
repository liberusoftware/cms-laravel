<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerApi\Http;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\ExtensionManager\Services\ExtensionManagerService;

final class ExtensionManagerController
{
    public function index(ExtensionManagerService $extensions): JsonResponse
    {
        return response()->json(['data' => $extensions->all()]);
    }

    public function enable(string $key, ExtensionManagerService $extensions): JsonResponse
    {
        $extensions->enable($key);

        return response()->json(['data' => ['key' => $key, 'enabled' => true]]);
    }

    public function disable(string $key, ExtensionManagerService $extensions): JsonResponse
    {
        $extensions->disable($key);

        return response()->json(['data' => ['key' => $key, 'enabled' => false]]);
    }
}
