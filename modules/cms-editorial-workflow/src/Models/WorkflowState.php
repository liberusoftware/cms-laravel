<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property string $label
 * @property bool $terminal
 */
final class WorkflowState extends Model
{
    #[\Override]
    protected $table = 'cms_editorial_workflow_states';

    #[\Override]
    protected $fillable = ['workflow_id', 'key', 'label', 'terminal'];

    protected function casts(): array
    {
        return ['terminal' => 'boolean'];
    }
}
