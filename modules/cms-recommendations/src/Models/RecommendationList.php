<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class RecommendationList extends Model
{
    use HasTenant;

    protected $table = 'cms_recommendation_lists';

    protected $fillable = ['name', 'key', 'kind', 'ranker', 'audience_rules', 'exclusions', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['audience_rules' => 'array', 'exclusions' => 'array', 'active' => 'boolean'];
    }

    /** @return HasMany<RecommendationItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(RecommendationItem::class, 'list_id');
    }
}
