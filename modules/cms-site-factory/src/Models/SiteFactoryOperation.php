<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteFactoryOperation extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_site_factory_operations';

    #[\Override]
    protected $fillable = ['site_id', 'team_id', 'operation', 'status', 'payload', 'error', 'completed_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'completed_at' => 'datetime'];
    }

    /** @return BelongsTo<Site, $this> */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
