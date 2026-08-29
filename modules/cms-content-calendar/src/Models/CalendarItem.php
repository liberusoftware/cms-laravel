<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class CalendarItem extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_calendar_items';

    #[\Override]
    protected $fillable = ['team_id', 'campaign_id', 'title', 'content_type', 'subject_key', 'channel', 'site', 'status', 'starts_at', 'deadline_at', 'assigned_to'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'deadline_at' => 'datetime'];
    }
}
