<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Delivery extends Model
{
    use HasTenant;

    protected $table = 'cms_subscription_deliveries';

    protected $fillable = ['subscription_id', 'event', 'channel', 'payload', 'status', 'attempts', 'sent_at', 'failed_at', 'team_id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'attempts' => 'integer', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
