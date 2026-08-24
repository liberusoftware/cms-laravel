<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ThemeInstallation extends Model
{
    use HasTenant;

    protected $table = 'cms_theme_installations';

    protected $fillable = ['theme_id', 'site_key', 'installed_version', 'status', 'installed_at', 'updated_at_version', 'team_id'];

    protected function casts(): array
    {
        return ['installed_at' => 'datetime'];
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(MarketplaceTheme::class, 'theme_id');
    }
}
