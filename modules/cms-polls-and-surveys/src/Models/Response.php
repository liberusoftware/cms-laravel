<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Response extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_poll_responses';

    #[\Override]
    protected $fillable = ['poll_id', 'user_id', 'respondent_hash', 'answers', 'submitted_at', 'team_id'];

    protected function casts(): array
    {
        return ['answers' => 'array', 'submitted_at' => 'datetime'];
    }

    /** @return BelongsTo<Poll, $this> */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }
}
