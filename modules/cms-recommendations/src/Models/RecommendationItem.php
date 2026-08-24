<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RecommendationItem extends Model
{
    protected $table = 'cms_recommendation_items';

    protected $fillable = ['list_id', 'item_type', 'item_key', 'title', 'summary', 'context', 'popularity_score', 'editorial_score', 'published_at', 'position'];

    protected function casts(): array
    {
        return ['context' => 'array', 'popularity_score' => 'float', 'editorial_score' => 'float', 'published_at' => 'datetime', 'position' => 'integer'];
    }

    /** @return BelongsTo<RecommendationList, $this> */
    public function list(): BelongsTo
    {
        return $this->belongsTo(RecommendationList::class, 'list_id');
    }
}
