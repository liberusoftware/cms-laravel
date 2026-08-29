<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ExtensionVersion extends Model
{
    protected $table = 'cms_extension_versions';

    protected $fillable = ['listing_id', 'version', 'download_url', 'checksum', 'signature', 'signing_key', 'status', 'released_at'];

    protected function casts(): array
    {
        return ['released_at' => 'datetime'];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(ExtensionListing::class, 'listing_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(ExtensionDistribution::class, 'version_id');
    }
}
