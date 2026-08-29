<?php

declare(strict_types=1);

namespace Liberu\Cms\Multisite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteReference extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_multisite_references';

    #[\Override]
    protected $fillable = ['source_site_id', 'target_site_id', 'content_type', 'content_id', 'mode', 'status', 'team_id'];

    /** @return BelongsTo<Site, $this> */
    public function sourceSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'source_site_id');
    }

    /** @return BelongsTo<Site, $this> */
    public function targetSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'target_site_id');
    }
}
