<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MediaTrack extends Model
{
    use HasTenant;

    protected $table = 'cms_media_tracks';

    protected $fillable = ['asset_id', 'track_type', 'language', 'label', 'uri', 'content', 'start_seconds', 'end_seconds', 'metadata', 'status', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'start_seconds' => 'float', 'end_seconds' => 'float'];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'asset_id');
    }
}
