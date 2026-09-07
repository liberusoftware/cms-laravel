<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property int $id
 * @property string $public_id
 * @property string $key
 * @property string $format
 * @property int $quality
 * @property int|null $width
 * @property int|null $height
 * @property string $fit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class ProcessingProfile extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_image_processing_profiles';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'key', 'format', 'quality', 'width', 'height', 'fit'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'quality' => 'integer', 'width' => 'integer', 'height' => 'integer'];
    }
}
