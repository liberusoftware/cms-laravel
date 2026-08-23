<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin;

use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

/**
 * In-memory catalogue of module-contributed admin surfaces. Registration is
 * de-duplicated per module so re-declaring the same class is harmless.
 */
final class AdminResourceRegistry implements AdminResourceRegistryInterface
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $resources = [];

    /**
     * @var array<string, array<int, string>>
     */
    private array $pages = [];

    public function registerResource(string $moduleKey, string $resourceClass): void
    {
        $this->add($this->resources, $moduleKey, $resourceClass);
    }

    public function resources(): array
    {
        return $this->resources;
    }

    public function registerPage(string $moduleKey, string $pageClass): void
    {
        $this->add($this->pages, $moduleKey, $pageClass);
    }

    public function pages(): array
    {
        return $this->pages;
    }

    /**
     * @param  array<string, array<int, string>>  $catalogue
     */
    private function add(array &$catalogue, string $moduleKey, string $class): void
    {
        $existing = $catalogue[$moduleKey] ?? [];

        if (in_array($class, $existing, true)) {
            return;
        }

        $existing[] = $class;
        $catalogue[$moduleKey] = $existing;
    }
}
