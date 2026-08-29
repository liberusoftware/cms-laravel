<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ExtensionPublisher extends Model
{
    use HasTenant;

    protected $table = 'cms_extension_publishers';

    protected $fillable = ['key', 'name', 'website', 'status', 'team_id'];

    public function listings(): HasMany
    {
        return $this->hasMany(ExtensionListing::class, 'publisher_id');
    }
}
