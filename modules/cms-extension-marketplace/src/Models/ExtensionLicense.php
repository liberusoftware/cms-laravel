<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ExtensionLicense extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_extension_licenses';

    #[\Override]
    protected $fillable = ['listing_id', 'license_key', 'subject_type', 'subject_id', 'status', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }
}
