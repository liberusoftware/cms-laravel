<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagement\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $team_id
 * @property string $name
 * @property string $asset_type
 * @property string $storage_key
 * @property Carbon|null $expires_at
 * @property array<string, mixed>|null $renditions
 * @property string $status
 * @property bool $brand_asset
 * @property bool $approved
 * @property Carbon|null $approved_at
 * @property array<string, mixed>|null $distribution
 */
final class DigitalAsset extends Model
{
    #[\Override]
    protected $table = 'cms_digital_assets';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'asset_type', 'storage_key', 'license', 'attribution', 'release_reference', 'expires_at', 'renditions', 'status', 'brand_asset', 'approved', 'approved_at', 'distribution'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'renditions' => 'array', 'brand_asset' => 'boolean', 'approved' => 'boolean', 'approved_at' => 'datetime', 'distribution' => 'array'];
    }
}
