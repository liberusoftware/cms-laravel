<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Variant extends Model
{
    use HasTenant;

    protected $table = 'cms_personalization_variants';

    protected $fillable = ['audience_id', 'key', 'payload', 'priority', 'holdout_percent', 'fallback', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'priority' => 'integer', 'holdout_percent' => 'integer', 'fallback' => 'boolean', 'active' => 'boolean'];
    }

    /** @return BelongsTo<Audience, $this> */
    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }
}
