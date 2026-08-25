<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Support;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\StaticPublishing\Contracts\DeploymentAdapterInterface;

final class DeploymentAdapterRegistry
{
    /** @var array<string, DeploymentAdapterInterface> */
    private array $adapters = [];

    public function register(DeploymentAdapterInterface $adapter): void
    {
        $this->adapters[$adapter->key()] = $adapter;
    }

    public function resolve(string $key): DeploymentAdapterInterface
    {
        if (! isset($this->adapters[$key])) {
            throw ValidationException::withMessages(['deployment' => "Unknown deployment adapter: {$key}."]);
        }

        return $this->adapters[$key];
    }
}
