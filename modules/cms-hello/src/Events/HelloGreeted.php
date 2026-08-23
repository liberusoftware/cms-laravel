<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Events;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * Emitted over the EventBus whenever a greeting is produced. Other modules may
 * listen for this without ever importing anything else from the Hello module.
 */
final readonly class HelloGreeted implements CmsEvent
{
    public function __construct(public string $name, public string $message) {}

    public function name(): string
    {
        return 'hello.greeted';
    }
}
