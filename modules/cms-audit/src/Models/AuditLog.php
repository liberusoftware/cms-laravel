<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * An append-only audit record.
 *
 * Records are written once and never changed: the model refuses updates and
 * deletes so history stays tamper-evident, and it tracks only `created_at`
 * (there is nothing to update). The admin viewer is read-only on top of this.
 *
 * @property int $id
 * @property string $action
 * @property string|null $actor_id
 * @property string|null $actor_label
 * @property string|null $subject_type
 * @property string|null $subject_id
 * @property int|null $team_id
 * @property string|null $ip_address
 * @property array<string, mixed>|null $metadata
 */
final class AuditLog extends Model
{
    public const UPDATED_AT = null;

    #[\Override]
    protected $table = 'cms_audit_logs';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'action', 'actor_id', 'actor_label', 'subject_type', 'subject_id',
        'team_id', 'ip_address', 'metadata',
    ];

    #[\Override]
    protected static function booted(): void
    {
        self::updating(function (): never {
            throw new RuntimeException('Audit log records are append-only and cannot be updated.');
        });

        self::deleting(function (): never {
            throw new RuntimeException('Audit log records are append-only and cannot be deleted.');
        });
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
