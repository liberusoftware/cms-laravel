<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExperimentObservation extends Model
{
    protected $table = 'cms_experiment_observations';

    protected $fillable = ['experiment_id', 'variant_id', 'subject_key', 'goal_key', 'value', 'observed_at'];

    protected function casts(): array
    {
        return ['value' => 'decimal:4', 'observed_at' => 'datetime'];
    }

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ExperimentVariant::class);
    }
}
