<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Liberu\Cms\Contracts\Events\Core\ContentIdentityCreated;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ContentIdentity extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_content_identities';

    #[\Override]
    protected $fillable = ['site_id', 'channel_id', 'content_type', 'content_id', 'canonical_path', 'status', 'owner_type', 'owner_id', 'metadata', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    protected static function booted(): void
    {
        self::created(function (ContentIdentity $identity): void {
            app(EventBusInterface::class)->dispatch(new ContentIdentityCreated($identity->getKey(), $identity->content_type, $identity->content_id));
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
