<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Models;

use Illuminate\Database\Eloquent\Model;

final class SiteTemplate extends Model
{
    #[\Override]
    protected $table = 'cms_site_templates';

    #[\Override]
    protected $fillable = ['key', 'name', 'configuration', 'initial_content', 'active'];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'initial_content' => 'array', 'active' => 'boolean'];
    }
}
