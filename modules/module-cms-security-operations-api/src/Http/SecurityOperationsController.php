<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsApi\Http;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\SecurityOperations\Models\SecurityOperation;

final class SecurityOperationsController
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => SecurityOperation::query()->latest()->paginate(50)]);
    }
}
