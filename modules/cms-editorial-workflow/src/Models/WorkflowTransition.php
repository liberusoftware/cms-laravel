<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $from_state
 * @property string $to_state
 * @property string|null $permission
 * @property bool $requires_review
 */
final class WorkflowTransition extends Model
{
    #[\Override]
    protected $table = 'cms_editorial_workflow_transitions';

    #[\Override]
    protected $fillable = ['workflow_id', 'from_state', 'to_state', 'permission', 'requires_review'];

    protected function casts(): array
    {
        return ['requires_review' => 'boolean'];
    }
}
