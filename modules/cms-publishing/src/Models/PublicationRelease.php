<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PublicationRelease extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_publication_releases';

    #[\Override]
    protected $attributes = ['state' => 'scheduled', 'targets' => '[]', 'cache_tags' => '[]'];

    #[\Override]
    protected $fillable = ['key', 'state', 'publish_at', 'embargo_until', 'expires_at', 'review_at', 'recurrence', 'targets', 'cache_tags', 'team_id'];

    protected function casts(): array
    {
        return ['publish_at' => 'datetime', 'embargo_until' => 'datetime', 'expires_at' => 'datetime', 'review_at' => 'datetime', 'targets' => 'array', 'cache_tags' => 'array'];
    }

    public function isDue(): bool
    {
        return $this->state === 'scheduled' && ($this->publish_at === null || $this->publish_at->isPast()) && ($this->embargo_until === null || $this->embargo_until->isPast());
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
