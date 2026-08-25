<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationSourceChange extends Model
{
    use HasTenant;

    protected $table = 'cms_translation_source_changes';

    protected $fillable = ['job_id', 'subject_type', 'subject_id', 'field', 'source_text', 'source_hash', 'source_version', 'translated_text', 'translated_hash', 'status', 'provider', 'model', 'cost', 'provenance', 'review_notes', 'translated_at', 'reviewed_at', 'team_id'];

    protected function casts(): array
    {
        return ['cost' => 'float', 'provenance' => 'array', 'review_notes' => 'array', 'translated_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(TranslationJob::class, 'job_id');
    }
}
