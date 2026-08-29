<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ConfigurationRelease extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_configuration_releases';

    #[\Override]
    protected $fillable = ['team_id', 'version', 'environment', 'payload', 'dependencies', 'checksum', 'status', 'created_by', 'promoted_at', 'rolled_back_at'];

    #[\Override]
    protected function casts(): array
    {
        return ['payload' => 'array', 'dependencies' => 'array', 'promoted_at' => 'datetime', 'rolled_back_at' => 'datetime'];
    }
}
