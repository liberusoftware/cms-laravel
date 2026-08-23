<?php

declare(strict_types=1);

namespace Liberu\Cms\Api;

use Liberu\Cms\Contracts\Preview\PreviewableSourceInterface;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;

/**
 * In-memory registry of the previewable sources modules contribute, keyed by
 * content type. Bound as a singleton before the content modules register, so
 * their `app()->bound(...)` guard passes.
 */
final class PreviewRegistry implements PreviewRegistryInterface
{
    /**
     * @var array<string, PreviewableSourceInterface>
     */
    private array $sources = [];

    public function registerSource(PreviewableSourceInterface $source): void
    {
        $this->sources[$source->typeKey()] = $source;
    }

    public function source(string $typeKey): ?PreviewableSourceInterface
    {
        return $this->sources[$typeKey] ?? null;
    }

    public function sources(): array
    {
        return $this->sources;
    }
}
