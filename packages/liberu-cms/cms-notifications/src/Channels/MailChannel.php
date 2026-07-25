<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Channels;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Liberu\Cms\Notifications\Contracts\NotificationChannelInterface;
use Liberu\Cms\Notifications\Messages\NotificationMessage;

/**
 * Delivers a notification as a plain-text email to the subscription's
 * recipients.
 */
final readonly class MailChannel implements NotificationChannelInterface
{
    public function __construct(private Mailer $mailer) {}

    public function key(): string
    {
        return 'mail';
    }

    public function send(NotificationMessage $message): void
    {
        if ($message->to === []) {
            return;
        }

        $this->mailer->raw($message->body, function (Message $mail) use ($message): void {
            $mail->to($message->to)->subject($message->subject);
        });
    }
}
