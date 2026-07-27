<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks;

/**
 * Marker for every value-transforming hook point ("filter").
 *
 * A filter is a mutable payload that flows through the HookBus: registered
 * callbacks receive the same instance in priority order and mutate its payload
 * in place, so an extension can *adapt* an existing value (rendered HTML, a form
 * schema, a query, an API payload) rather than only add a sibling or observe an
 * event. Neither the emitting site nor a listener imports the other.
 *
 * @api This interface is part of the public extension API.
 */
interface Filter
{
    /**
     * Stable dot-notation name of the filter point, e.g. "blocks.render".
     * Used for logging and diagnostics; dispatch is keyed by the concrete class.
     */
    public function name(): string;
}
