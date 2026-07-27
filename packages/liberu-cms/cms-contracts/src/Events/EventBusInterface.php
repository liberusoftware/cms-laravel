<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events;

use Closure;

/**
 * The single seam through which modules broadcast and observe cross-module
 * events. Wrapping the framework dispatcher keeps the boundary type-safe
 * (only CmsEvent instances cross it) and provides one place to later add
 * cross-application transport without touching any module.
 *
 * @api This interface is part of the public extension API.
 */
interface EventBusInterface
{
    /**
     * Dispatch a cross-module event to all registered listeners.
     */
    public function dispatch(CmsEvent $event): void;

    /**
     * Register a listener for a cross-module event class.
     *
     * @param  class-string<CmsEvent>  $eventClass
     * @param  Closure|class-string|array{0: class-string, 1: string}  $listener
     */
    public function listen(string $eventClass, Closure|string|array $listener): void;
}
