<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ThemeComponent extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_theme_components';

    #[\Override]
    protected $fillable = ['theme_key', 'region', 'component_key', 'view_contract', 'configuration', 'team_id'];

    protected function casts(): array
    {
        return ['view_contract' => 'array', 'configuration' => 'array'];
    }
}
