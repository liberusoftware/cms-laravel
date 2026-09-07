<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ExtensionListing extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_extension_listings';

    #[\Override]
    protected $fillable = ['publisher_id', 'category_id', 'key', 'name', 'description', 'license', 'status', 'security_status', 'metadata', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(ExtensionPublisher::class, 'publisher_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExtensionCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ExtensionVersion::class, 'listing_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ExtensionReview::class, 'listing_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(ExtensionLicense::class, 'listing_id');
    }

    public function trials(): HasMany
    {
        return $this->hasMany(ExtensionTrial::class, 'listing_id');
    }

    public function support(): HasMany
    {
        return $this->hasMany(ExtensionSupport::class, 'listing_id');
    }
}
