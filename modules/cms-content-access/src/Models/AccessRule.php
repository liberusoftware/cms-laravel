<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess\Models;

use Illuminate\Database\Eloquent\Model;

final class AccessRule extends Model
{
    #[\Override]
    protected $table = 'cms_content_access_rules';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'visibility', 'audiences', 'fields', 'available_from', 'available_until', 'preview_allowed'];

    protected function casts(): array
    {
        return ['audiences' => 'array', 'fields' => 'array', 'available_from' => 'datetime', 'available_until' => 'datetime', 'preview_allowed' => 'boolean'];
    }
}
