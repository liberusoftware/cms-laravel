<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class StaticBuild extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_static_builds';

    #[\Override]
    protected $fillable = ['site_key', 'state', 'kind', 'deployment', 'manifest', 'diagnostics', 'parent_build_id', 'checksum', 'started_at', 'finished_at', 'team_id'];

    protected function casts(): array
    {
        return ['manifest' => 'array', 'diagnostics' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'parent_build_id' => 'integer'];
    }
}
