<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $article_id @property int|null $team_id @property bool $helpful */
final class KnowledgeFeedback extends Model
{
    #[\Override]
    protected $table = 'cms_knowledge_base_feedback';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'helpful' => 'boolean'];
}
