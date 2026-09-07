<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ExtensionDistribution extends Model
{
    #[\Override]
    protected $table = 'cms_extension_distributions';

    #[\Override]
    protected $fillable = ['version_id', 'channel', 'url', 'checksum', 'status'];

    public function version(): BelongsTo
    {
        return $this->belongsTo(ExtensionVersion::class, 'version_id');
    }
}
