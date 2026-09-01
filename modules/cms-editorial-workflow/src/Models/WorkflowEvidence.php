<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;

final class WorkflowEvidence extends Model
{
    #[\Override]
    protected $table = 'cms_editorial_workflow_evidence';

    #[\Override]
    protected $fillable = ['workflow_id', 'subject_type', 'subject_key', 'event', 'actor_key', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
