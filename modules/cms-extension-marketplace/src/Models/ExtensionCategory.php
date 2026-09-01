<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ExtensionCategory extends Model
{
    #[\Override]
    protected $table = 'cms_extension_categories';

    #[\Override]
    protected $fillable = ['key', 'name', 'description'];

    public function listings(): HasMany
    {
        return $this->hasMany(ExtensionListing::class, 'category_id');
    }
}
