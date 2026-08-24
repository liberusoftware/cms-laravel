<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Contracts\Events\Core\SiteCreated;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Site extends Model
{
    use HasTenant;

    protected $table = 'cms_sites';

    protected $fillable = ['key', 'name', 'domain', 'default_locale', 'timezone', 'status', 'settings', 'team_id'];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    protected static function booted(): void
    {
        self::created(function (Site $site): void {
            app(EventBusInterface::class)->dispatch(new SiteCreated($site->getKey(), $site->key));
        });
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(ContentIdentity::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(ContentAlias::class);
    }

    public function cmsSettings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
