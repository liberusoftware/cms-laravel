<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContent\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class RelatedContent extends Model
{
    use HasTenant;

    protected $table = 'cms_related_content';

    protected $fillable = ['source_type', 'source_id', 'target_type', 'target_id', 'mode', 'score', 'explanation', 'taxonomy', 'excluded', 'team_id'];

    protected function casts(): array
    {
        return ['score' => 'float', 'explanation' => 'array', 'taxonomy' => 'array', 'excluded' => 'boolean'];
    }
}
