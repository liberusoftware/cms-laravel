<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $entitlement_key
 * @property int $delay_days
 */
final class DripSchedule extends Model
{
    #[\Override]
    protected $table = 'cms_membership_drip_schedules';

    #[\Override]
    protected $fillable = ['membership_content_id', 'entitlement_key', 'delay_days'];

    protected function casts(): array
    {
        return ['delay_days' => 'integer'];
    }

    public function delayDays(): int
    {
        $value = $this->getAttribute('delay_days');

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
