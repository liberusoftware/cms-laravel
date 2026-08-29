<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\SiteFactory\Models\SiteDomain;
use Liberu\Cms\SiteFactory\Models\SiteTemplate;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

final class SiteFactoryController
{
    public function index(Request $request): JsonResponse
    {
        $size = min(max($request->integer('page.size', 25), 1), 100);
        $sites = Site::query()->when($request->string('filter')->toString() !== '', fn ($query) => $query->where('key', 'like', '%'.$request->string('filter')->toString().'%'))->orderBy('created_at', 'desc')->paginate($size);
        return response()->json(['data' => $sites->getCollection()->map(fn (Site $site): array => $this->site($site))->values()->all(), 'meta' => ['current_page' => $sites->currentPage(), 'per_page' => $sites->perPage(), 'total' => $sites->total()], 'links' => ['next' => $sites->nextPageUrl(), 'previous' => $sites->previousPageUrl()]]);
    }

    public function store(Request $request, SiteFactoryService $service): JsonResponse
    {
        $data = $this->validated($request, ['key' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'template' => ['sometimes', 'nullable', 'string'], 'domain' => ['sometimes', 'nullable', 'string', 'max:255']]);
        return response()->json(['data' => $this->site($service->provision($this->stringInput($data, 'key'), $this->stringInput($data, 'name'), $this->nullableStringInput($data, 'template'), $this->nullableStringInput($data, 'domain'), $request->user()?->current_team_id))], 201);
    }

    public function show(int $id): JsonResponse { return response()->json(['data' => $this->site(Site::query()->findOrFail($id))]); }

    public function update(Request $request, int $id, SiteFactoryService $service): JsonResponse
    {
        $site = Site::query()->findOrFail($id); $data = $this->validated($request, ['name' => ['sometimes', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:active,suspended,archived']]);
        if (isset($data['status'])) $service->transition($site, $this->stringInput($data, 'status'));
        if (isset($data['name'])) $site->forceFill(['name' => $this->stringInput($data, 'name')])->save();
        return response()->json(['data' => $this->site($site->refresh())]);
    }

    public function destroy(int $id, SiteFactoryService $service): JsonResponse { $service->archive(Site::query()->findOrFail($id)); return response()->json(status: 204); }

    public function templates(SiteFactoryService $service): JsonResponse { return response()->json(['data' => $service->templates()->map(fn (SiteTemplate $template): array => $this->templateData($template))->values()->all()]); }

    public function template(Request $request, SiteFactoryService $service): JsonResponse
    {
        $data = $this->validated($request, ['key' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'configuration' => ['sometimes', 'array'], 'initial_content' => ['sometimes', 'array']]);
        return response()->json(['data' => $this->templateData($service->template($this->stringInput($data, 'key'), $this->stringInput($data, 'name'), $this->arrayInput($data, 'configuration'), $this->arrayInput($data, 'initial_content'), $request->user()?->current_team_id))], 201);
    }

    public function cloneById(Request $request, int $id, SiteFactoryService $service): JsonResponse { $data = $this->validated($request, ['key' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255']]); return response()->json(['data' => $this->site($service->clone(Site::query()->findOrFail($id), $this->stringInput($data, 'key'), $this->stringInput($data, 'name')))], 201); }
    public function suspendById(int $id, SiteFactoryService $service): JsonResponse { return response()->json(['data' => $this->site($service->suspend(Site::query()->findOrFail($id)))]); }
    public function archiveById(int $id, SiteFactoryService $service): JsonResponse { return response()->json(['data' => $this->site($service->archive(Site::query()->findOrFail($id)))]); }
    public function teardownById(Request $request, int $id, SiteFactoryService $service): JsonResponse { $service->teardown(Site::query()->findOrFail($id), $request->boolean('confirm')); return response()->json(status: 204); }

    public function suspend(string $site, SiteFactoryService $service): JsonResponse { return $this->suspendById($this->siteId($site), $service); }
    public function archive(string $site, SiteFactoryService $service): JsonResponse { return $this->archiveById($this->siteId($site), $service); }
    public function clone(Request $request, string $site, SiteFactoryService $service): JsonResponse { return $this->cloneById($request, $this->siteId($site), $service); }
    public function verifyDomain(Request $request, int $domain, SiteFactoryService $service): JsonResponse { $data = $this->validated($request, ['token' => ['required', 'string']]); return response()->json(['data' => $this->domain($service->verifyDomain(SiteDomain::query()->findOrFail($domain), $this->stringInput($data, 'token')))]); }
    public function teardown(Request $request, string $site, SiteFactoryService $service): JsonResponse { return $this->teardownById($request, $this->siteId($site), $service); }

    /** @return array<string, mixed> */ private function site(Site $site): array { return $site->only(['id', 'key', 'name', 'domain', 'default_locale', 'timezone', 'status', 'settings', 'team_id', 'created_at', 'updated_at']); }
    /** @return array<string, mixed> */ private function templateData(SiteTemplate $template): array { return $template->only(['id', 'key', 'name', 'configuration', 'initial_content', 'active', 'team_id', 'created_at', 'updated_at']); }
    /** @return array<string, mixed> */ private function domain(SiteDomain $domain): array { return $domain->only(['id', 'site_id', 'domain', 'verified_at', 'team_id', 'created_at', 'updated_at']); }

    /**
     * @param array<string, array<int, mixed>> $rules
     * @return array<string|int, mixed>
     */
    private function validated(Request $request, array $rules): array
    {
        $data = $request->validate($rules);
        if (! is_array($data)) {
            throw new \UnexpectedValueException('Validation did not return an array.');
        }

        return $data;
    }

    /**
     * @param array<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    private function arrayInput(array $data, string $key): array
    {
        return is_array($data[$key] ?? null) ? $data[$key] : [];
    }

    /** @param array<string|int, mixed> $data */
    private function stringInput(array $data, string $key): string
    {
        return is_string($data[$key] ?? null) ? $data[$key] : throw new \UnexpectedValueException("Missing string input: {$key}");
    }

    /** @param array<string|int, mixed> $data */
    private function nullableStringInput(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return $value === null ? null : (is_string($value) ? $value : throw new \UnexpectedValueException("Invalid string input: {$key}"));
    }

    private function siteId(string $key): int
    {
        $id = Site::query()->where('key', $key)->firstOrFail()->getKey();

        return is_int($id) ? $id : throw new \UnexpectedValueException('Site key did not resolve to an integer ID.');
    }
}
