<?php

declare(strict_types=1);

namespace Liberu\Cms\Multisite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteQuota extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_multisite_quotas';

    #[\Override]
    protected $fillable = ['site_id', 'limits', 'usage', 'team_id'];

    protected function casts(): array
    {
        return ['limits' => 'array', 'usage' => 'array'];
    }

    /** @return BelongsTo<Site, $this> */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
