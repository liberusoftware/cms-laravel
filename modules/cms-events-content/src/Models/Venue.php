<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Venue extends Model
{
    use HasTenant;

    protected $table = 'cms_event_venues';

    protected $fillable = ['name', 'address', 'timezone', 'latitude', 'longitude', 'team_id'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
