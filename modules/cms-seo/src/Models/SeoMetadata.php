<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SeoMetadata extends Model
{
    use HasTenant;

    protected $table = 'cms_seo_metadata';

    protected $fillable = ['seoable_type', 'seoable_id', 'title', 'description', 'canonical_url', 'robots', 'structured_data', 'social_cards', 'hreflang', 'noindex', 'noarchive', 'team_id'];

    protected function casts(): array
    {
        return ['structured_data' => 'array', 'social_cards' => 'array', 'hreflang' => 'array', 'noindex' => 'boolean', 'noarchive' => 'boolean'];
    }
}
