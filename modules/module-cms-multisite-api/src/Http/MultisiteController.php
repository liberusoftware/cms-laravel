<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Multisite\MultisiteService;

final class MultisiteController
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Site::query()->orderBy('key')->get()->map(fn (Site $site): array => $this->site($site))->all()]);
    }

    public function store(Request $request, MultisiteService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'domain' => ['sometimes', 'nullable', 'string', 'max:255'], 'team_id' => ['sometimes', 'nullable', 'integer']]);

        return response()->json(['data' => $this->site($service->createSite($data['key'], $data['name'], $data['domain'] ?? null, $data['team_id'] ?? null))], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => $this->site(Site::query()->findOrFail($id))]);
    }

    public function update(Request $request, int $id, MultisiteService $service): JsonResponse
    {
        $site = Site::query()->findOrFail($id);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:active,suspended,archived'], 'settings' => ['sometimes', 'array']]);
        if (isset($data['status'])) {
            $service->transition($site, $data['status']);
        }
        $site->fill(array_diff_key($data, ['status' => true]))->save();

        return response()->json(['data' => $this->site($site->refresh())]);
    }

    public function admin(Request $request, int $id, MultisiteService $service): JsonResponse
    {
        $data = $request->validate(['user_id' => ['required', 'integer'], 'role' => ['required', 'string'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->assignAdmin(Site::query()->findOrFail($id), (int) $data['user_id'], $data['role'], (bool) ($data['active'] ?? true))->toArray()], 201);
    }

    public function quota(Request $request, int $id, MultisiteService $service): JsonResponse
    {
        $data = $request->validate(['limits' => ['required', 'array']]);

        return response()->json(['data' => $service->setQuota(Site::query()->findOrFail($id), $data['limits'])->toArray()]);
    }

    public function reference(Request $request, int $id, MultisiteService $service): JsonResponse
    {
        $data = $request->validate(['target_site_id' => ['required', 'integer'], 'content_type' => ['required', 'string', 'max:255'], 'content_id' => ['required', 'string', 'max:255'], 'mode' => ['sometimes', 'string', 'in:shared,isolated']]);

        return response()->json(['data' => $service->reference(Site::query()->findOrFail($id), Site::query()->findOrFail($data['target_site_id']), $data['content_type'], $data['content_id'], $data['mode'] ?? 'shared')->toArray()], 201);
    }

    public function networkTransition(Request $request, MultisiteService $service): JsonResponse
    {
        $data = $request->validate(['site_ids' => ['required', 'array', 'min:1'], 'site_ids.*' => ['integer'], 'status' => ['required', 'string', 'in:active,suspended,archived']]);

        return response()->json(['updated' => $service->networkTransition($data['site_ids'], $data['status'])]);
    }

    private function site(Site $site): array
    {
        return $site->only(['id', 'key', 'name', 'domain', 'default_locale', 'timezone', 'status', 'settings', 'team_id']);
    }
}
