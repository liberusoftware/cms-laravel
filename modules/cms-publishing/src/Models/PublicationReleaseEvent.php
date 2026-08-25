<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PublicationReleaseEvent extends Model
{
    #[\Override]
    protected $table = 'cms_publication_release_events';

    #[\Override]
    protected $fillable = ['release_id', 'event', 'payload', 'occurred_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }

    /** @return BelongsTo<PublicationRelease, $this> */
    public function release(): BelongsTo
    {
        return $this->belongsTo(PublicationRelease::class, 'release_id');
    }
}
