<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MarketplaceTheme extends Model
{
    use HasTenant;

    protected $table = 'cms_marketplace_themes';

    protected $fillable = ['key', 'name', 'version', 'author', 'description', 'manifest', 'compatibility', 'preview_url', 'license', 'parent_key', 'status', 'security_status', 'team_id'];

    protected function casts(): array
    {
        return ['manifest' => 'array', 'compatibility' => 'array'];
    }

    public function installations(): HasMany
    {
        return $this->hasMany(ThemeInstallation::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(ThemeRating::class, 'theme_id');
    }
}
