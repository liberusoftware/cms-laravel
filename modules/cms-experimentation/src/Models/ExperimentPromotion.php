<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExperimentPromotion extends Model
{
    #[\Override]
    protected $table = 'cms_experiment_promotions';

    #[\Override]
    protected $fillable = ['experiment_id', 'variant_id', 'actor_type', 'actor_id', 'reason', 'promoted_at'];

    protected function casts(): array
    {
        return ['promoted_at' => 'datetime'];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ExperimentVariant::class);
    }
}
