<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiApi\Http;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\HeadlessApi\Services\PersistedQueryService;
use Liberu\Cms\HeadlessApiApi\Http\Resources\PersistedQueryResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PersistedQueryController
{
    public function store(Request $request, PersistedQueryService $service): PersistedQueryResource
    {
        $data = $request->validate(['query' => ['required', 'string', 'max:100000']]);
        if (! is_array($data) || ! is_string($data['query'] ?? null)) {
            throw ValidationException::withMessages(['query' => 'The query payload is invalid.']);
        }

        return new PersistedQueryResource($service->store($data['query'], $request->user()?->current_team_id));
    }

    public function resolve(string $hash, Request $request, PersistedQueryService $service): PersistedQueryResource
    {
        $query = $service->resolve($hash, $request->user()?->current_team_id);
        if (! $query) {
            throw new NotFoundHttpException;
        }

        return new PersistedQueryResource($query);
    }
}
