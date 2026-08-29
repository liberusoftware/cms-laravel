<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class FederationSource extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_federation_sources';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'adapter', 'endpoint', 'status', 'last_checked_at', 'last_succeeded_at', 'last_error'];

    protected function casts(): array
    {
        return ['last_checked_at' => 'datetime', 'last_succeeded_at' => 'datetime'];
    }
}
