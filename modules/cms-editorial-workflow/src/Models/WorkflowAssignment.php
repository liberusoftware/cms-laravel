<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;

final class WorkflowAssignment extends Model
{
    #[\Override]
    protected $table = 'cms_editorial_workflow_assignments';

    #[\Override]
    protected $fillable = ['workflow_id', 'subject_type', 'subject_key', 'actor_type', 'actor_key', 'role', 'status', 'delegated_from_id'];
}
