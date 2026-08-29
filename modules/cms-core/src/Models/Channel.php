<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Cms\Contracts\Events\Core\ChannelCreated;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int $site_id
 * @property string $key
 * @property string $name
 * @property string $type
 * @property array<string, mixed>|null $settings
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
final class Channel extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_channels';

    #[\Override]
    protected $fillable = ['site_id', 'key', 'name', 'type', 'settings', 'team_id'];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    protected static function booted(): void
    {
        self::created(function (Channel $channel): void {
            app(EventBusInterface::class)->dispatch(new ChannelCreated($channel->getKey(), $channel->site_id, $channel->key));
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(ContentIdentity::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(ContentAlias::class);
    }
}
