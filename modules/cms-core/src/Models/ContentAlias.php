<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ContentAlias extends Model
{
    use HasTenant;

    protected $table = 'cms_content_aliases';

    protected $fillable = ['site_id', 'channel_id', 'alias', 'target_type', 'target_id', 'redirect_status'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
