<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class CalendarCampaign extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_calendar_campaigns';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'slug', 'description', 'status'];
}
