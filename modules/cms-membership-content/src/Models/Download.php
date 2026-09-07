<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Download extends Model
{
    #[\Override]
    protected $table = 'cms_membership_downloads';

    #[\Override]
    protected $fillable = ['membership_content_id', 'public_id', 'path', 'filename', 'mime_type', 'size', 'checksum'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    /** @return BelongsTo<MembershipContent, $this> */
    public function content(): BelongsTo
    {
        return $this->belongsTo(MembershipContent::class, 'membership_content_id');
    }
}
