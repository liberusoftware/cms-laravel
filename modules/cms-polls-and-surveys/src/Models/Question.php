<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Question extends Model
{
    #[\Override]
    protected $table = 'cms_poll_questions';

    #[\Override]
    protected $fillable = ['poll_id', 'key', 'type', 'prompt', 'options', 'branching', 'position', 'required'];

    protected function casts(): array
    {
        return ['options' => 'array', 'branching' => 'array', 'position' => 'integer', 'required' => 'boolean'];
    }

    /** @return BelongsTo<Poll, $this> */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }
}
