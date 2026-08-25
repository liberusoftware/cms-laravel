<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Liberu\Cms\CoreApi\Http\Resources\CoreAliasResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreChannelResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreIdentityResource;
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
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['sometimes', 'nullable', 'url', 'max:255'],
            'default_locale' => ['sometimes', 'string', 'max:16'],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'status' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return (new CoreSiteResource($this->mutations->createSite($data)))->response()->setStatusCode(201);
    }

    public function createChannel(Request $request, string $site): JsonResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:64'],
            'settings' => ['sometimes', 'nullable', 'array'],
        ]);

        return (new CoreChannelResource($this->mutations->createChannel($site, $data)))->response()->setStatusCode(201);
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
}
