<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;
use Liberu\Cms\OfflineAndPwa\Services\OfflineAndPwaService;

final class OfflineAndPwaController
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => PwaConfiguration::query()->orderBy('site_key')->get()->map(fn (PwaConfiguration $configuration): array => $this->payload($configuration))->values()->all()]);
    }

    public function store(Request $request, OfflineAndPwaService $service): JsonResponse
    {
        $data = $request->validate($this->rules(true));
        $configuration = $service->configure($data['site_key'], $data['name'], $data['short_name'], attributes: $data);

        return response()->json(['data' => $this->payload($configuration)], 201);
    }

    public function show(Request $request, OfflineAndPwaService $service): JsonResponse
    {
        $data = $request->validate(['site_key' => ['required', 'string', 'max:100'], 'asset' => ['sometimes', 'string', 'in:manifest,service-worker']]);
        $configuration = PwaConfiguration::query()->where('site_key', $data['site_key'])->firstOrFail();
        $asset = $data['asset'] ?? 'manifest';
        if ($asset === 'service-worker') {
            return response($service->serviceWorker($configuration), 200, ['Content-Type' => 'application/javascript; charset=UTF-8', 'Cache-Control' => 'no-cache']);
        }

        return response()->json(['data' => $service->manifest($configuration), 'service_worker_version' => $configuration->service_worker_version]);
    }

    public function showById(int $id): JsonResponse
    {
        return response()->json(['data' => $this->payload($this->configuration($id))]);
    }

    public function update(Request $request, int $id, OfflineAndPwaService $service): JsonResponse
    {
        $configuration = $service->update($this->configuration($id), $request->validate($this->rules(false)));

        return response()->json(['data' => $this->payload($configuration)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->configuration($id)->delete();

        return response()->json(status: 204);
    }

    public function cachePolicy(Request $request, int $id, OfflineAndPwaService $service): JsonResponse
    {
        $configuration = $service->setCachePolicy($this->configuration($id), $request->validate(['precache' => ['sometimes', 'array'], 'runtime' => ['sometimes', 'array'], 'max_entries' => ['sometimes', 'integer', 'min:1'], 'max_age_seconds' => ['sometimes', 'integer', 'min:1'], 'offline_fallback' => ['sometimes', 'boolean']]));

        return response()->json(['data' => $this->payload($configuration)]);
    }

    public function publishUpdate(Request $request, int $id, OfflineAndPwaService $service): JsonResponse
    {
        $data = $request->validate(['version' => ['sometimes', 'string', 'max:255']]);

        return response()->json(['data' => $this->payload($service->publishUpdate($this->configuration($id), $data['version'] ?? null))]);
    }

    private function configuration(int $id): PwaConfiguration
    {
        return PwaConfiguration::query()->findOrFail($id);
    }

    /** @return array<string, mixed> */
    private function payload(PwaConfiguration $configuration): array
    {
        return $configuration->only(['id', 'site_key', 'name', 'short_name', 'start_url', 'scope', 'display', 'theme_color', 'background_color', 'icon_url', 'offline_url', 'cache_policy', 'service_worker_version', 'last_updated_at']);
    }

    /** @return array<string, array<int, mixed>> */
    private function rules(bool $required): array
    {
        return [
            'site_key' => [$required ? 'required' : 'sometimes', 'string', 'max:100'],
            'name' => [$required ? 'required' : 'sometimes', 'string', 'max:255'],
            'short_name' => [$required ? 'required' : 'sometimes', 'string', 'max:12'],
            'start_url' => ['sometimes', 'string', 'max:255'], 'scope' => ['sometimes', 'string', 'max:255'],
            'display' => ['sometimes', 'string', 'in:fullscreen,standalone,minimal-ui,browser'],
            'theme_color' => ['sometimes', 'nullable', 'string', 'max:32'], 'background_color' => ['sometimes', 'nullable', 'string', 'max:32'],
            'icon_url' => ['sometimes', 'nullable', 'string', 'max:2048'], 'offline_url' => ['sometimes', 'string', 'max:255'],
            'cache_policy' => ['sometimes', 'array'], 'service_worker_version' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
