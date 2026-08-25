<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

final class ContentInsight extends Model
{
    #[\Override]
    protected $table = 'cms_content_intelligence_insights';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'metric', 'score', 'severity', 'summary', 'rationale', 'context', 'status', 'reviewed_at'];

    protected function casts(): array
    {
        return ['score' => 'float', 'context' => 'array', 'reviewed_at' => 'datetime'];
    }
}
