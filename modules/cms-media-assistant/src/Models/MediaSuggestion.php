<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $public_id
 * @property string $asset_key
 * @property string $kind
 * @property string $value
 * @property string $provider
 * @property string|null $model
 * @property float|null $confidence
 * @property array<string, mixed>|null $provenance
 * @property string $status
 * @property string|null $reviewer_key
 * @property string|null $review_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class MediaSuggestion extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_media_assistant_suggestions';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'asset_key', 'kind', 'value', 'provider', 'model', 'confidence', 'provenance', 'status', 'reviewer_key', 'review_note'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'confidence' => 'float', 'provenance' => 'array'];
    }
}
