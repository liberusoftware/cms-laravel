<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Block;

/**
 * The catalogue of available block types. Any module may register its own block
 * types here so the renderer can resolve them by key; the content-blocks module
 * seeds the prebuilt types. Resolved through this contract so a registering
 * module never imports the concrete registry.
 *
 * @api This interface is part of the public extension API.
 */
interface BlockTypeRegistryInterface
{
    public function register(BlockTypeInterface $type): void;

    public function get(string $key): ?BlockTypeInterface;

    public function has(string $key): bool;

    /**
     * @return array<string, BlockTypeInterface>
     */
    public function all(): array;
}
