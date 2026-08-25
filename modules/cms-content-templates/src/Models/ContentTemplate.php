<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplates\Models;

use Illuminate\Database\Eloquent\Model;

final class ContentTemplate extends Model
{
    #[\Override]
    protected $table = 'cms_content_templates';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'slug', 'content_type', 'version', 'schema', 'defaults', 'locked', 'published', 'rollout_percent'];

    protected function casts(): array
    {
        return ['schema' => 'array', 'defaults' => 'array', 'locked' => 'boolean', 'published' => 'boolean'];
    }
}
