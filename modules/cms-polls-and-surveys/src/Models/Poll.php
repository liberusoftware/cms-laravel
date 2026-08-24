<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Poll extends Model
{
    use HasTenant;

    protected $table = 'cms_polls';

    protected $fillable = ['title', 'key', 'description', 'starts_at', 'ends_at', 'allow_anonymous', 'allow_multiple', 'active', 'results_public', 'team_id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'allow_anonymous' => 'boolean', 'allow_multiple' => 'boolean', 'active' => 'boolean', 'results_public' => 'boolean'];
    }

    /** @return HasMany<Question, $this> */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    /** @return HasMany<Response, $this> */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function isOpen(): bool
    {
        return $this->active && ($this->starts_at === null || $this->starts_at->isPast()) && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}
