<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Api;

/**
 * The catalogue of Delivery API endpoints modules contribute to the API.
 *
 * A module announces the endpoint(s) it owns during the register phase, tagged
 * with its module key. The API module reads the catalogue in boot() to define
 * the versioned route group — so the API surface tracks the installed modules
 * without the API module ever importing one. Mirrors AdminResourceRegistry.
 *
 * @api This interface is part of the public extension API.
 */
interface ApiResourceRegistryInterface
{
    /**
     * Announce a Delivery API endpoint owned by a module.
     *
     * @param  string  $moduleKey  The owning module's key, e.g. "pages".
     */
    public function registerEndpoint(string $moduleKey, ApiEndpoint $endpoint): void;

    /**
     * Every registered endpoint, grouped by the owning module key.
     *
     * @return array<string, array<int, ApiEndpoint>>
     */
    public function endpoints(): array;
}
