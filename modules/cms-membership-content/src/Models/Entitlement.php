<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $subject_type
 * @property string $subject_key
 * @property string $entitlement_key
 * @property Carbon|null $starts_at
 * @property Carbon|null $expires_at
 */
final class Entitlement extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_membership_entitlements';

    #[\Override]
    protected $fillable = ['team_id', 'subject_type', 'subject_key', 'entitlement_key', 'external_id', 'starts_at', 'expires_at'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'starts_at' => 'datetime', 'expires_at' => 'datetime'];
    }
}
