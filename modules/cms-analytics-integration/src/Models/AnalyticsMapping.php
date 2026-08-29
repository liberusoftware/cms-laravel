<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class AnalyticsMapping extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_analytics_mappings';

    #[\Override]
    protected $fillable = ['team_id', 'event_type', 'provider', 'measurement_key', 'consent_category', 'config', 'enabled'];

    protected function casts(): array
    {
        return ['config' => 'array', 'enabled' => 'boolean'];
    }
}
