<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $public_id
 * @property string $asset_key
 * @property int $profile_id
 * @property string $source_checksum
 * @property string $path
 * @property string $status
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class ImageDerivative extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_image_processing_derivatives';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'asset_key', 'profile_id', 'source_checksum', 'path', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'metadata' => 'array'];
    }
}
