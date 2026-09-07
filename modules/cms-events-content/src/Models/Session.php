<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Session extends Model
{
    #[\Override]
    protected $table = 'cms_event_sessions';

    #[\Override]
    protected $fillable = ['event_id', 'key', 'title', 'description', 'starts_at', 'ends_at', 'room'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'cms_event_speaker_session');
    }
}
