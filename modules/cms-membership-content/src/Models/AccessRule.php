<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property string $entitlement_key */
final class AccessRule extends Model
{
    #[\Override]
    protected $table = 'cms_membership_access_rules';

    #[\Override]
    protected $fillable = ['membership_content_id', 'entitlement_key', 'minimum_days'];

    protected function casts(): array
    {
        return ['minimum_days' => 'integer'];
    }

    public function minimumDays(): int
    {
        $value = $this->getAttribute('minimum_days');

        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (int) $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }

    /** @return BelongsTo<MembershipContent, $this> */
    public function content(): BelongsTo
    {
        return $this->belongsTo(MembershipContent::class, 'membership_content_id');
    }
}
