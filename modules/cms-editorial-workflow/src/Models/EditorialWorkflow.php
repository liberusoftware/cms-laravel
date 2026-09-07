<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $public_id
 * @property string $key
 * @property string $name
 * @property string $initial_state
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class EditorialWorkflow extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_editorial_workflows';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'key', 'name', 'initial_state'];

    /** @return HasMany<WorkflowState, $this> */
    public function states(): HasMany
    {
        return $this->hasMany(WorkflowState::class, 'workflow_id');
    }

    /** @return HasMany<WorkflowTransition, $this> */
    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'workflow_id');
    }

    /** @return HasMany<WorkflowAssignment, $this> */
    public function assignments(): HasMany
    {
        return $this->hasMany(WorkflowAssignment::class, 'workflow_id');
    }
}
