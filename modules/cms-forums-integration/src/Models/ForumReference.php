<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property int $id
 * @property string $public_id
 * @property string $provider
 * @property string $external_type
 * @property string $external_id
 * @property string|null $url
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $last_activity_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class ForumReference extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_forum_references';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'metadata' => 'array', 'last_activity_at' => 'datetime'];
}
