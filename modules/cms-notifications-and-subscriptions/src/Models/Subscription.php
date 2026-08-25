<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Subscription extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_subscriptions';

    #[\Override]
    protected $fillable = ['subscriber_type', 'subscriber_id', 'subject_type', 'subject_id', 'frequency', 'channels', 'locale', 'active', 'unsubscribed_at', 'team_id'];

    protected function casts(): array
    {
        return ['channels' => 'array', 'active' => 'boolean', 'unsubscribed_at' => 'datetime'];
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
