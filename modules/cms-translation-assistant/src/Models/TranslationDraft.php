<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationDraft extends Model
{
    use HasTenant;

    protected $table = 'cms_translation_drafts';

    protected $fillable = ['subject_type', 'subject_id', 'source_locale', 'target_locale', 'source_text', 'translated_text', 'confidence', 'status', 'provider', 'model', 'provenance', 'violations', 'reviewer_type', 'reviewer_id', 'reviewed_at', 'team_id'];

    protected function casts(): array
    {
        return ['confidence' => 'float', 'provenance' => 'array', 'violations' => 'array', 'reviewed_at' => 'datetime'];
    }
}
