<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar\Models;

use Illuminate\Database\Eloquent\Model;

final class CalendarCampaign extends Model
{
    #[\Override]
    protected $table = 'cms_calendar_campaigns';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'slug', 'description', 'status'];
}
