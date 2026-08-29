<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int $site_id
 * @property int|null $channel_id
 * @property string $alias
 * @property string $target_type
 * @property string $target_id
 * @property int $redirect_status
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
final class ContentAlias extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_content_aliases';

    #[\Override]
    protected $fillable = ['site_id', 'channel_id', 'alias', 'target_type', 'target_id', 'redirect_status', 'team_id'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
