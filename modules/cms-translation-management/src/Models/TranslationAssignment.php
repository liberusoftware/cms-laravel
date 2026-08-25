<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationAssignment extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_translation_assignments';

    #[\Override]
    protected $fillable = ['job_id', 'source_change_id', 'assignee_type', 'assignee_id', 'role', 'status', 'due_at', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(TranslationJob::class, 'job_id');
    }

    public function sourceChange(): BelongsTo
    {
        return $this->belongsTo(TranslationSourceChange::class, 'source_change_id');
    }
}
