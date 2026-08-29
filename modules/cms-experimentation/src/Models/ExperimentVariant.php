<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExperimentVariant extends Model
{
    protected $table = 'cms_experiment_variants';

    protected $fillable = ['experiment_id', 'key', 'name', 'content', 'weight'];

    protected function casts(): array
    {
        return ['content' => 'array', 'weight' => 'integer'];
    }

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }
}
