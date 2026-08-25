<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class DigitalAsset extends Model
{
    protected $table = 'cms_digital_assets';

    protected $fillable = ['team_id', 'name', 'asset_type', 'storage_key', 'license', 'attribution', 'release_reference', 'expires_at', 'renditions', 'status', 'brand_asset', 'approved', 'approved_at', 'distribution'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'renditions' => 'array', 'brand_asset' => 'boolean', 'approved' => 'boolean', 'approved_at' => 'datetime', 'distribution' => 'array'];
    }
}
