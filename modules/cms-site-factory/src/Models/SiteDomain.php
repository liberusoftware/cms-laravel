<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteDomain extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_site_domains';

    #[\Override]
    protected $fillable = ['site_id', 'domain', 'verification_token', 'verified_at', 'team_id'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    /** @return BelongsTo<Site, $this> */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
