<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ThemeBinding extends Model
{
    use HasTenant;

    protected $table = 'cms_theme_bindings';

    protected $fillable = ['site_key', 'channel_key', 'theme_key', 'fallback_theme_key', 'preview_token', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
