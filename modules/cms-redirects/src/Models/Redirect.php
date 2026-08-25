<?php

declare(strict_types=1);

namespace Liberu\Cms\Redirects\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Redirect extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_redirects';

    #[\Override]
    protected $fillable = ['from_path', 'to_path', 'status_code', 'hit_count', 'expires_at', 'active', 'source', 'team_id'];

    protected function casts(): array
    {
        return ['status_code' => 'integer', 'hit_count' => 'integer', 'expires_at' => 'datetime', 'active' => 'boolean'];
    }

    public function isValid(): bool
    {
        return $this->active && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
