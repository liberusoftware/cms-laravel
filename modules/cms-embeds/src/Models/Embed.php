<?php

namespace Liberu\Cms\Embeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

class Embed extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_embeds';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['metadata' => 'array', 'responsive' => 'boolean', 'consent_required' => 'boolean'];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
