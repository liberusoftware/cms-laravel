<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class BlockDocument extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_block_documents';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'blocks', 'version', 'locked', 'preview_html'];

    protected function casts(): array
    {
        return ['blocks' => 'array', 'version' => 'integer', 'locked' => 'boolean'];
    }
}
