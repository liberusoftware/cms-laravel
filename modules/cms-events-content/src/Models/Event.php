<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Event extends Model
{
    use HasTenant;

    protected $table = 'cms_events';

    protected $fillable = ['key', 'title', 'description', 'status', 'timezone', 'starts_at', 'ends_at', 'archived_at', 'structured_data', 'venue_id', 'team_id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'archived_at' => 'datetime', 'structured_data' => 'array'];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(RegistrationReference::class);
    }
}
