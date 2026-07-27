<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events;

/**
 * Marker for every event that crosses a module boundary.
 *
 * Cross-module communication happens only through events implementing this
 * interface, dispatched via the EventBus. A module emits; other modules listen.
 * Neither side imports the other's concrete classes.
 *
 * @api This interface is part of the public extension API.
 */
interface CmsEvent
{
    /**
     * Stable dot-notation name of the event, e.g. "hello.greeted",
     * "pages.published". Used for logging, webhooks, and cross-app transport.
     */
    public function name(): string;
}
