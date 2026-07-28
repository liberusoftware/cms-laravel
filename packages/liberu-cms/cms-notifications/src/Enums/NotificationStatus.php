<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Enums;

use Liberu\Cms\Notifications\Models\NotificationLog;

/**
 * The delivery lifecycle of a {@see NotificationLog}
 * row: queued (`Pending`), delivered (`Sent`), or exhausted its retries
 * (`Failed`). `Sent` is what the retry idempotency guard checks to avoid
 * re-sending a notification that already went out.
 */
enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}
