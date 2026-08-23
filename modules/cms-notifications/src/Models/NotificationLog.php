<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Notifications\Enums\NotificationStatus;

/**
 * An audit record of a notification the system decided to send, written before
 * the delivery job is queued and updated to its terminal status by the job.
 *
 * @property int $id
 * @property string $event
 * @property string $channel
 * @property string|null $recipient
 * @property int|null $team_id
 * @property array<string, mixed>|null $context
 * @property NotificationStatus $status
 */
final class NotificationLog extends Model
{
    #[\Override]
    protected $table = 'cms_notification_logs';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['event', 'channel', 'recipient', 'team_id', 'context', 'status'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'context' => 'array',
            'status' => NotificationStatus::class,
        ];
    }
}
