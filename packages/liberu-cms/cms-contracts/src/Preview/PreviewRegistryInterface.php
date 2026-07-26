<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Preview;

/**
 * The catalogue of previewable sources modules contribute. A module announces
 * its source; the preview endpoint reads the catalogue to resolve a type key to
 * its model and resource — so preview tracks the installed modules without
 * importing one. Mirrors the admin, API, search, and sitemap registries.
 */
interface PreviewRegistryInterface
{
    public function registerSource(PreviewableSourceInterface $source): void;

    /**
     * The source for a type key, or null when none is registered.
     */
    public function source(string $typeKey): ?PreviewableSourceInterface;

    /**
     * @return array<string, PreviewableSourceInterface>
     */
    public function sources(): array;
}
