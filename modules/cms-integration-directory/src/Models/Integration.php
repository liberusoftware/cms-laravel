<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectory\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Integration extends Model
{
    use HasTenant;

    protected $table = 'cms_integrations';

    protected $fillable = ['key', 'name', 'provider', 'category', 'configuration', 'status', 'health_status', 'health_message', 'last_checked_at', 'team_id'];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'last_checked_at' => 'datetime'];
    }
}
