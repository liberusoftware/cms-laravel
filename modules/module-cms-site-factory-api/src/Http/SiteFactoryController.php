<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\SiteFactory\Models\SiteDomain;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

final class SiteFactoryController
{
    public function store(Request $request, SiteFactoryService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'alpha_dash'], 'name' => ['required', 'string', 'max:255'], 'template' => ['sometimes', 'nullable', 'string'], 'domain' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => $service->provision($data['key'], $data['name'], $data['template'] ?? null, $data['domain'] ?? null, $request->user()?->current_team_id)], 201);
    }

    public function template(Request $request, SiteFactoryService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string'], 'name' => ['required', 'string'], 'configuration' => ['array'], 'initial_content' => ['array']]);

        return response()->json(['data' => $service->template($data['key'], $data['name'], $data['configuration'] ?? [], $data['initial_content'] ?? [])], 201);
    }

    public function suspend(string $site, SiteFactoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->suspend(Site::query()->where('key', $site)->firstOrFail())]);
    }

    public function archive(string $site, SiteFactoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->archive(Site::query()->where('key', $site)->firstOrFail())]);
    }

    public function clone(Request $request, string $site, SiteFactoryService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'alpha_dash'], 'name' => ['required', 'string', 'max:255']]);

        return response()->json(['data' => $service->clone(Site::query()->where('key', $site)->firstOrFail(), $data['key'], $data['name'])], 201);
    }

    public function verifyDomain(Request $request, int $domain, SiteFactoryService $service): JsonResponse
    {
        $token = $request->validate(['token' => ['required', 'string']])['token'];

        return response()->json(['data' => $service->verifyDomain(SiteDomain::query()->findOrFail($domain), $token)]);
    }

    public function teardown(Request $request, string $site, SiteFactoryService $service): JsonResponse
    {
        $confirmed = $request->boolean('confirm');
        $service->teardown(Site::query()->where('key', $site)->firstOrFail(), $confirmed);

        return response()->json([], 204);
    }
}
