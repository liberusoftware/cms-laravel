<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Preview;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A content type that can be previewed before publication. Each content module
 * implements this for its own model and registers it with the preview registry,
 * so the preview endpoint can resolve any type without importing a module.
 * Lookups return the item regardless of workflow state; the shared tenancy scope
 * still constrains them to the current tenant.
 */
interface PreviewableSourceInterface
{
    /**
     * The content type key this source previews (e.g. "pages").
     */
    public function typeKey(): string;

    /**
     * The item by id regardless of workflow state, or null when it does not
     * exist for the current tenant.
     */
    public function find(int $id): ?Model;

    /**
     * Wrap a previewed model in the module's Delivery API resource.
     */
    public function toResource(Model $model): JsonResource;
}
