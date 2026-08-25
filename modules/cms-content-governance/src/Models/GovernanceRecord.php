<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernance\Models;

use Illuminate\Database\Eloquent\Model;

final class GovernanceRecord extends Model
{
    protected $table = 'cms_content_governance_records';

    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'owner_id', 'steward_id', 'policy_labels', 'classification', 'review_due_at', 'retention_until', 'legal_hold', 'legal_hold_at', 'legal_hold_reason', 'evidence'];

    protected function casts(): array
    {
        return ['policy_labels' => 'array', 'review_due_at' => 'datetime', 'retention_until' => 'datetime', 'legal_hold' => 'boolean', 'legal_hold_at' => 'datetime', 'evidence' => 'array'];
    }
}
