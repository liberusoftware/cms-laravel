<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks;

use Closure;

/**
 * The single seam through which modules intercept and transform each other's
 * values. Where the EventBus broadcasts fire-and-forget notifications, the
 * HookBus runs an ordered pipeline of callbacks that mutate a typed Filter
 * payload — the one primitive that lets an extension adapt existing behaviour.
 *
 * Dispatch is keyed by the concrete Filter class, so any module may define new
 * filter points (its own Filter subclasses) as well as hook the curated core
 * points. Callbacks run in ascending priority order (lower first; equal
 * priorities keep registration order) and mutate the filter in place; the return
 * value of a callback is ignored.
 *
 * @api This interface is part of the public extension API.
 */
interface HookBusInterface
{
    /**
     * Register a callback against a filter point.
     *
     * @param  class-string<Filter>  $filterClass
     * @param  Closure|class-string|array{0: class-string, 1: string}  $callback
     * @param  int  $priority  Lower runs first; defaults to 0.
     */
    public function listen(string $filterClass, Closure|string|array $callback, int $priority = 0): void;

    /**
     * Run every callback registered for the filter's concrete class, in priority
     * order, each mutating the payload in place, then return the same instance.
     *
     * @template T of Filter
     *
     * @param  T  $filter
     * @return T
     */
    public function apply(Filter $filter): Filter;
}
