<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MediaAsset extends Model
{
    use HasTenant;
    protected $table = 'cms_media_assets';
    protected $fillable = ['public_id', 'title', 'kind', 'source_type', 'source_uri', 'mime_type', 'bytes', 'duration_seconds', 'stream_uri', 'poster_uri', 'status', 'metadata', 'checksum', 'team_id'];
    protected function casts(): array { return ['metadata' => 'array']; }
    protected static function booted(): void { self::creating(function (self $asset): void { $asset->public_id ??= (string) Str::uuid(); }); }
    public function tracks(): HasMany { return $this->hasMany(MediaTrack::class, 'asset_id'); }
    public function variants(): HasMany { return $this->hasMany(MediaVariant::class, 'asset_id'); }
}
