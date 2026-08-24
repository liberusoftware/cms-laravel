<?php

declare(strict_types=1);

use Illuminate\Contracts\Mail\Mailer;
use Liberu\Cms\Notifications\Channels\MailChannel;
use Liberu\Cms\Notifications\Messages\NotificationMessage;

it('sends a plain-text email through the mailer', function (): void {
    $mailer = Mockery::mock(Mailer::class);
    $mailer->shouldReceive('raw')->once();

    new MailChannel($mailer)->send(
        new NotificationMessage('mail', ['a@example.com'], 'Subject', 'Body', 'forms.submitted'),
    );
});

it('does not send when there are no recipients', function (): void {
    $mailer = Mockery::mock(Mailer::class);
    $mailer->shouldNotReceive('raw');

    new MailChannel($mailer)->send(
        new NotificationMessage('mail', [], 'Subject', 'Body', 'forms.submitted'),
    );

    expect(true)->toBeTrue();
});
