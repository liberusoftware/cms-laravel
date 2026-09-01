<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;

final class WorkflowReview extends Model
{
    #[\Override]
    protected $table = 'cms_editorial_workflow_reviews';

    #[\Override]
    protected $fillable = ['workflow_id', 'subject_type', 'subject_key', 'reviewer_key', 'decision', 'comment'];
}
