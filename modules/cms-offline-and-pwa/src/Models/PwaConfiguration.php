<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwa\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PwaConfiguration extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_pwa_configurations';

    #[\Override]
    protected $fillable = ['site_key', 'name', 'short_name', 'start_url', 'scope', 'display', 'theme_color', 'background_color', 'icon_url', 'offline_url', 'cache_policy', 'service_worker_version', 'last_updated_at', 'team_id'];

    protected function casts(): array
    {
        return ['cache_policy' => 'array', 'last_updated_at' => 'datetime'];
    }
}
