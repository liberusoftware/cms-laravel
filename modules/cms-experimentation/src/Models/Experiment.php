<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Experiment extends Model
{
    use HasTenant;

    protected $table = 'cms_experiments';

    protected $fillable = ['key', 'name', 'type', 'status', 'allocation_percentage', 'goals', 'guardrails', 'analysis_policy', 'winner_variant_key', 'team_id'];

    protected function casts(): array
    {
        return ['goals' => 'array', 'guardrails' => 'array', 'analysis_policy' => 'array', 'allocation_percentage' => 'integer'];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ExperimentVariant::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(ExperimentObservation::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(ExperimentPromotion::class);
    }
}
