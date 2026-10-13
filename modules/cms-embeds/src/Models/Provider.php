<?php

namespace Liberu\Cms\Embeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

class Provider extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_embed_providers';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['config' => 'array'];

    public function embeds(): HasMany
    {
        return $this->hasMany(Embed::class);
    }
}
