<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PrivateLink extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_content_private_links';

    #[\Override]
    protected $fillable = ['team_id', 'token_hash', 'subject_type', 'subject_key', 'expires_at', 'uses', 'max_uses', 'revoked_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'revoked_at' => 'datetime'];
    }
}
