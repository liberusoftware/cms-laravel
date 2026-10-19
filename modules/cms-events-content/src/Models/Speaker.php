<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Speaker extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_event_speakers';

    #[\Override]
    protected $fillable = ['name', 'bio', 'email', 'metadata', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(Session::class, 'cms_event_speaker_session');
    }
}
