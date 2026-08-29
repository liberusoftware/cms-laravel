<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class BackupSchedule extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_backup_schedules';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'frequency', 'artifact_types', 'retention_days', 'enabled', 'next_run_at', 'last_run_at'];

    protected function casts(): array
    {
        return ['artifact_types' => 'array', 'retention_days' => 'integer', 'enabled' => 'boolean', 'next_run_at' => 'datetime', 'last_run_at' => 'datetime'];
    }
}
