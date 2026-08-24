<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperations\Models;

use Illuminate\Database\Eloquent\Model;

final class SecurityOperation extends Model
{
    protected $table = 'cms_security_operations';

    protected $fillable = ['kind', 'subject', 'status', 'evidence', 'content_hash', 'actor_id'];

    protected function casts(): array
    {
        return ['evidence' => 'array', 'actor_id' => 'integer'];
    }
}
