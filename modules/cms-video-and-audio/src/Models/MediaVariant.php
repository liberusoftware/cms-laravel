<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MediaVariant extends Model
{
    use HasTenant;
    protected $table = 'cms_media_variants';
    protected $fillable = ['asset_id', 'idempotency_key', 'adapter', 'profile', 'uri', 'status', 'bytes', 'metadata', 'failure_reason', 'team_id'];
    protected function casts(): array { return ['metadata' => 'array']; }
    public function asset(): BelongsTo { return $this->belongsTo(MediaAsset::class, 'asset_id'); }
}
