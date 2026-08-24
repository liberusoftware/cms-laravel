<?php

declare(strict_types=1);

namespace Liberu\Cms\Sitemaps\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SitemapEntry extends Model
{
    use HasTenant;

    protected $table = 'cms_sitemap_entries';

    protected $fillable = ['site_id', 'type', 'locale', 'url', 'last_modified', 'priority', 'change_frequency', 'images', 'video', 'news', 'excluded', 'team_id'];

    protected function casts(): array
    {
        return ['last_modified' => 'datetime', 'priority' => 'float', 'images' => 'array', 'video' => 'array', 'news' => 'array', 'excluded' => 'boolean'];
    }
}
