<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property string $visibility
 * @property array<int, string>|null $audiences
 * @property bool $preview_allowed
 * @property Carbon|null $available_from
 * @property Carbon|null $available_until
 */
final class AccessRule extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_content_access_rules';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'visibility', 'audiences', 'fields', 'available_from', 'available_until', 'preview_allowed'];

    protected function casts(): array
    {
        return ['audiences' => 'array', 'fields' => 'array', 'available_from' => 'datetime', 'available_until' => 'datetime', 'preview_allowed' => 'boolean'];
    }
}
