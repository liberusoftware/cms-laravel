<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * An audit record of a notification the system decided to send, written before
 * the delivery job is queued.
 *
 * @property int $id
 * @property string $event
 * @property string $channel
 * @property string|null $recipient
 * @property int|null $team_id
 * @property array<string, mixed>|null $context
 */
final class NotificationLog extends Model
{
    #[\Override]
    protected $table = 'cms_notification_logs';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['event', 'channel', 'recipient', 'team_id', 'context'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}
