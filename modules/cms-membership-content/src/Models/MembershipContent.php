<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $status
 * @property Carbon|null $available_from
 * @property Carbon|null $available_until
 */
final class MembershipContent extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_membership_contents';

    /** @var list<string> */
    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'title', 'subject_type', 'subject_key', 'status', 'description', 'available_from', 'available_until'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'available_from' => 'datetime', 'available_until' => 'datetime'];
    }

    /** @return HasMany<AccessRule, $this> */
    public function accessRules(): HasMany
    {
        return $this->hasMany(AccessRule::class);
    }

    /** @return HasMany<DripSchedule, $this> */
    public function dripSchedules(): HasMany
    {
        return $this->hasMany(DripSchedule::class);
    }

    /** @return HasMany<Download, $this> */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
