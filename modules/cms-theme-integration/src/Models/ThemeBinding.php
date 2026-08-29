<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ThemeBinding extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_theme_bindings';

    #[\Override]
    protected $fillable = ['site_key', 'channel_key', 'theme_key', 'fallback_theme_key', 'preview_token', 'preview_expires_at', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'preview_expires_at' => 'datetime'];
    }
}
