<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLocking\Models;

use Illuminate\Database\Eloquent\Model;

final class ContentLock extends Model
{
    protected $table = 'cms_content_locks';

    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'holder_id', 'token', 'version', 'snapshot', 'expires_at'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'expires_at' => 'datetime'];
    }
}
