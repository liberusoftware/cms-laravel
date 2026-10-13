<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $article_id
 * @property int $version
 * @property string $body
 */
final class KnowledgeArticleVersion extends Model
{
    #[\Override]
    protected $table = 'cms_knowledge_base_versions';

    #[\Override]
    protected $guarded = [];
}
