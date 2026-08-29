<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RegistrationReference extends Model
{
    protected $table = 'cms_event_registrations';

    protected $fillable = ['event_id', 'provider', 'external_key', 'url', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
