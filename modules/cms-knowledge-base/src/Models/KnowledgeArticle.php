<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $slug
 * @property string $public_id
 * @property string $title
 * @property string $body
 * @property string $status
 * @property int $search_weight
 * @property int|null $parent_id
 * @property Carbon|null $published_at
 * @property Carbon|null $review_due_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class KnowledgeArticle extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_knowledge_base_articles';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'search_weight' => 'integer', 'published_at' => 'datetime', 'review_due_at' => 'datetime'];

    /** @return HasMany<KnowledgeArticleVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(KnowledgeArticleVersion::class, 'article_id');
    }

    /** @return BelongsTo<self, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
