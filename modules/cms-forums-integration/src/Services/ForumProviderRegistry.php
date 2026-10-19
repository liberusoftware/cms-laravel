<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration\Services;

use InvalidArgumentException;
use Liberu\Cms\ForumsIntegration\Contracts\ForumProvider;

final class ForumProviderRegistry
{
    /** @var array<string, ForumProvider> */
    private array $providers = [];

    public function register(string $key, ForumProvider $provider): void
    {
        if (! preg_match('/^[a-z0-9][a-z0-9_.-]*$/', $key)) {
            throw new InvalidArgumentException('Forum provider keys must be lowercase identifiers.');
        }
        $this->providers[$key] = $provider;
    }

    public function get(string $key): ForumProvider
    {
        return $this->providers[$key] ?? throw new InvalidArgumentException('The forum provider is not registered.');
    }
}
