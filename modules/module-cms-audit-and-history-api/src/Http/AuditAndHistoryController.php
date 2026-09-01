<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Audit\Models\AuditLog;
use Liberu\Cms\AuditAndHistoryApi\Http\Resources\AuditLogResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AuditAndHistoryController
{
    public function index(Request $request): JsonResponse
    {
        $size = max(1, min($request->integer('page_size', 25), 100));
        $query = AuditLog::query()->latest('created_at');
        $action = $request->input('action');
        if (is_string($action) && $action !== '') {
            $query->where('action', $action);
        }

        return response()->json(['data' => AuditLogResource::collection($query->limit($size)->get())]);
    }

    public function show(int|string $audit): AuditLogResource
    {
        $log = AuditLog::query()->find($audit);
        if ($log === null) {
            throw new NotFoundHttpException;
        }

        return new AuditLogResource($log);
    }
}
