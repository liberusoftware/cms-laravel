<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Models\Channel;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Liberu\Cms\CoreApi\Http\Resources\CoreAliasResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreChannelResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreIdentityResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreSettingResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreSiteResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CoreApiController
{
    public function __construct(
        private CoreQueryService $queries,
        private CoreMutationService $mutations,
    ) {}

    public function createSite(Request $request): JsonResponse
    {
        $data = $this->validated($request, [
            'key' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['sometimes', 'nullable', 'url', 'max:255'],
            'default_locale' => ['sometimes', 'string', 'max:16'],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'status' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return new CoreSiteResource($this->mutations->createSite($data))->response()->setStatusCode(201);
    }

    public function createChannel(Request $request, string $site): JsonResponse
    {
        $data = $this->validated($request, [
            'key' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return new CoreChannelResource($this->mutations->createChannel($site, $data))->response()->setStatusCode(201);
    }

    public function sites(): AnonymousResourceCollection
    {
        return CoreSiteResource::collection($this->queries->sites(request()->integer('per_page', 15)));
    }

    public function site(string $site): CoreSiteResource
    {
        $record = $this->queries->site($site);
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return new CoreSiteResource($record);
    }

    public function channels(string $site): AnonymousResourceCollection
    {
        try {
            $channels = $this->queries->channels($site, request()->integer('per_page', 15));
        } catch (ModelNotFoundException) {
            throw new NotFoundHttpException;
        }

        return CoreChannelResource::collection($channels);
    }

    public function alias(string $site, string $alias): CoreAliasResource
    {
        $record = $this->queries->alias($site, $alias);
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return new CoreAliasResource($record);
    }

    public function aliases(string $site): AnonymousResourceCollection
    {
        return CoreAliasResource::collection($this->queries->aliases($site, request()->integer('per_page', 15)));
    }

    public function identities(string $site): AnonymousResourceCollection
    {
        return CoreIdentityResource::collection($this->queries->identities($site, request()->integer('per_page', 15)));
    }

    public function updateSite(Request $request, string $site): CoreSiteResource
    {
        $data = $this->validated($request, [
            'key' => ['sometimes', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'domain' => ['sometimes', 'nullable', 'url', 'max:255'],
            'default_locale' => ['sometimes', 'string', 'max:16'],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'status' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return new CoreSiteResource($this->mutations->updateSite($this->siteRecord($site), $data));
    }

    public function destroySite(string $site): JsonResponse
    {
        $this->mutations->deleteSite($this->siteRecord($site));

        return response()->json(status: 204);
    }

    public function updateChannel(Request $request, string $site, string $channel): CoreChannelResource
    {
        $data = $this->validated($request, [
            'key' => ['sometimes', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return new CoreChannelResource($this->mutations->updateChannel($this->channel($site, $channel), $data));
    }

    public function destroyChannel(string $site, string $channel): JsonResponse
    {
        $this->mutations->deleteChannel($this->channel($site, $channel));

        return response()->json(status: 204);
    }

    public function createIdentity(Request $request, string $site): JsonResponse
    {
        $data = $this->validated($request, [
            'channel_id' => ['sometimes', 'nullable', 'integer'],
            'content_type' => ['required', 'string', 'max:255'],
            'content_id' => ['required', 'string', 'max:255'],
            'canonical_path' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:64'],
            'owner_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'owner_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ]);

        return new CoreIdentityResource($this->mutations->createIdentity($site, $data))->response()->setStatusCode(201);
    }

    public function createAlias(Request $request, string $site): JsonResponse
    {
        $data = $this->validated($request, [
            'channel_id' => ['sometimes', 'nullable', 'integer'],
            'alias' => ['required', 'string', 'max:255'],
            'target_type' => ['required', 'string', 'max:255'],
            'target_id' => ['required', 'string', 'max:255'],
            'redirect_status' => ['sometimes', 'integer', 'in:301,302,307,308'],
        ]);

        return new CoreAliasResource($this->mutations->createAlias($site, $data))->response()->setStatusCode(201);
    }

    public function settings(string $site): AnonymousResourceCollection
    {
        return CoreSettingResource::collection($this->queries->settings($site, request()->integer('per_page', 15), request()->string('environment', 'production')->toString()));
    }

    public function putSetting(Request $request, string $site, string $key): CoreSettingResource
    {
        $data = $this->validated($request, [
            'value' => ['required', 'array'],
            'environment' => ['sometimes', 'string', 'max:32'],
        ]);

        $value = $this->associative($data['value'] ?? null);
        $environment = $data['environment'] ?? 'production';

        if (! is_string($environment)) {
            throw new \UnexpectedValueException('Invalid setting payload.');
        }

        return new CoreSettingResource($this->mutations->putSetting($this->siteRecord($site), $key, $value, $environment));
    }

    /**
     * @param  array<string, array<int, mixed>>  $rules
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $rules): array
    {
        $data = $request->validate($rules);

        if (! is_array($data)) {
            return [];
        }

        $validated = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /** @return array<string, mixed> */
    private function associative(mixed $value): array
    {
        if (! is_array($value)) {
            throw new \UnexpectedValueException('An associative setting value is required.');
        }

        $result = [];
        foreach ($value as $key => $item) {
            if (! is_string($key)) {
                throw new \UnexpectedValueException('Setting values must use string keys.');
            }
            $result[$key] = $item;
        }

        return $result;
    }

    private function siteRecord(string $site): Site
    {
        return Site::query()->where('key', $site)->firstOrFail();
    }

    private function channel(string $site, string $channel): Channel
    {
        $siteRecord = $this->siteRecord($site);

        return Channel::query()->where('site_id', $siteRecord->getKey())->where(function ($query) use ($channel): void {
            $query->where('key', $channel);
            if (is_numeric($channel)) {
                $query->orWhere('id', (int) $channel);
            }
        })->firstOrFail();
    }
}
