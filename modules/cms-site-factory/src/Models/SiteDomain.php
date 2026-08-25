<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Models;

use Illuminate\Database\Eloquent\Model;

final class SiteDomain extends Model
{
    #[\Override]
    protected $table = 'cms_site_domains';

    #[\Override]
    protected $fillable = ['site_id', 'domain', 'verification_token', 'verified_at'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }
}
