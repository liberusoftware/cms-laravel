<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PageAlias extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_page_aliases';

    #[\Override]
    protected $fillable = ['page_id', 'path', 'team_id'];

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
