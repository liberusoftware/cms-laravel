<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteTemplate extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_site_templates';

    #[\Override]
    protected $fillable = ['key', 'name', 'configuration', 'initial_content', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'initial_content' => 'array', 'active' => 'boolean'];
    }
}
