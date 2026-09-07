<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLocking\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $subject_type
 * @property string $subject_key
 * @property int|null $holder_id
 * @property string $token
 * @property int $version
 * @property array<string, mixed>|null $snapshot
 * @property Carbon $expires_at
 */
final class ContentLock extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_content_locks';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'holder_id', 'token', 'version', 'snapshot', 'expires_at'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'expires_at' => 'datetime'];
    }
}
