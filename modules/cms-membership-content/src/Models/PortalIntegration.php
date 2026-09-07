<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PortalIntegration extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_membership_portal_integrations';

    #[\Override]
    protected $fillable = ['team_id', 'provider', 'external_id', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'metadata' => 'array'];
    }
}
