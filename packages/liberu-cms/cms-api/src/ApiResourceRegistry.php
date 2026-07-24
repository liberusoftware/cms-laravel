<?php

declare(strict_types=1);

namespace Liberu\Cms\Api;

use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

/**
 * In-memory catalogue of module-contributed Delivery API endpoints, grouped by
 * the owning module key. Mirrors AdminResourceRegistry.
 */
final class ApiResourceRegistry implements ApiResourceRegistryInterface
{
    /**
     * @var array<string, array<int, ApiEndpoint>>
     */
    private array $endpoints = [];

    public function registerEndpoint(string $moduleKey, ApiEndpoint $endpoint): void
    {
        $this->endpoints[$moduleKey][] = $endpoint;
    }

    public function endpoints(): array
    {
        return $this->endpoints;
    }
}
