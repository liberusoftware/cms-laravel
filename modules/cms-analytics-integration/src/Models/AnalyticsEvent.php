<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class AnalyticsEvent extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_analytics_events';

    #[\Override]
    protected $fillable = ['team_id', 'event_type', 'event_name', 'subject_type', 'subject_id', 'consent_category', 'consent_granted', 'status', 'idempotency_key', 'payload', 'occurred_at'];

    protected function casts(): array
    {
        return ['consent_granted' => 'boolean', 'payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
