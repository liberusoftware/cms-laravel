<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class IntegrityScan extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_integrity_scans';

    #[\Override]
    protected $fillable = ['team_id', 'scope', 'status', 'finding_count', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
