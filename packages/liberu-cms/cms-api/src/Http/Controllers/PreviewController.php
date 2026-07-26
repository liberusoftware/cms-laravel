<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves a signed preview link to a single draft-inclusive item, scoped to the
 * tenant named in the (signed) link so it can never reveal another tenant's
 * content or a different item than the one the link was minted for. The
 * signature middleware rejects tampered or expired links with a 403 before this
 * runs; an unknown type or a missing item yields a 404 without leaking existence.
 */
final readonly class PreviewController
{
    public function __construct(
        private PreviewRegistryInterface $registry,
        private TenantContextInterface $tenant,
    ) {}

    public function __invoke(Request $request, string $type, int $id): JsonResource
    {
        $source = $this->registry->source($type);

        if ($source === null) {
            throw new NotFoundHttpException;
        }

        $this->tenant->setTenantId($request->integer('team') ?: null);

        $model = $source->find($id);

        if ($model === null) {
            throw new NotFoundHttpException;
        }

        return $source->toResource($model);
    }
}
