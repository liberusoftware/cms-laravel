<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/** @property int|null $team_id */
final class IntegrityFinding extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_integrity_findings';

    #[\Override]
    protected $fillable = ['scan_id', 'team_id', 'subject_type', 'subject_key', 'kind', 'severity', 'message', 'context', 'status', 'resolved_at'];

    protected function casts(): array
    {
        return ['context' => 'array', 'resolved_at' => 'datetime'];
    }
}
