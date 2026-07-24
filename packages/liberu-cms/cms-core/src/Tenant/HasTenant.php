<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Contracts\Tenancy\TenantModelResolverInterface;

/**
 * Makes a module model tenant-scoped via a `team_id` column and a `team()`
 * relationship to the host's tenant model — resolved through a contract, so the
 * module never imports a host class. This satisfies Filament's tenant ownership
 * relationship for module resources.
 *
 * @mixin Model
 */
trait HasTenant
{
    public static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function team(): BelongsTo
    {
        $model = app(TenantModelResolverInterface::class)->tenantModel() ?? Model::class;

        return $this->belongsTo($model, 'team_id');
    }
}
