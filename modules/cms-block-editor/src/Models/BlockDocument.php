<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $subject_type
 * @property string $subject_id
 * @property array<int, mixed> $blocks
 * @property int $version
 * @property bool $locked
 * @property string|null $preview_html
 */
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
