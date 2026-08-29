<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class BackupArtifact extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_backup_artifacts';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'artifact_type', 'status', 'disk', 'path', 'size', 'checksum', 'encrypted', 'metadata', 'verified_at', 'expires_at'];

    protected function casts(): array
    {
        return ['size' => 'integer', 'encrypted' => 'boolean', 'metadata' => 'array', 'verified_at' => 'datetime', 'expires_at' => 'datetime'];
    }
}
