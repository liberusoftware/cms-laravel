<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks;

use Liberu\Cms\Contracts\Block\BlockTypeInterface;
use Liberu\Cms\Contracts\Block\BlockTypeRegistryInterface;

/**
 * The catalogue of available block types. Modules and themes register their own
 * types here; the renderer resolves them by key.
 */
final class BlockTypeRegistry implements BlockTypeRegistryInterface
{
    /**
     * @var array<string, BlockTypeInterface>
     */
    private array $types = [];

    public function register(BlockTypeInterface $type): void
    {
        $this->types[$type->key()] = $type;
    }

    public function get(string $key): ?BlockTypeInterface
    {
        return $this->types[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->types[$key]);
    }

    /**
     * @return array<string, BlockTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }
}
