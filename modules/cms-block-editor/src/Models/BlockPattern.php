<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class BlockPattern extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_block_patterns';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'blocks', 'reusable', 'locked'];

    protected function casts(): array
    {
        return ['blocks' => 'array', 'reusable' => 'boolean', 'locked' => 'boolean'];
    }
}
