<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Models;

use Illuminate\Database\Eloquent\Model;

final class StaticBuild extends Model
{
    protected $table = 'cms_static_builds';

    protected $fillable = ['site_key', 'state', 'kind', 'deployment', 'manifest', 'diagnostics', 'parent_build_id', 'checksum', 'started_at', 'finished_at'];

    protected function casts(): array
    {
        return ['manifest' => 'array', 'diagnostics' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'parent_build_id' => 'integer'];
    }
}
