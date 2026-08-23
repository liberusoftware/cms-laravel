<?php

declare(strict_types=1);

namespace Liberu\Cms\Users\Access;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;

/**
 * In-memory catalogue of every permission group modules declare. Registration
 * is idempotent per group key so re-declaring is safe.
 */
final class PermissionRegistrar implements PermissionRegistrarInterface
{
    /**
     * @var array<string, PermissionGroup>
     */
    private array $groups = [];

    public function register(PermissionGroup $group): void
    {
        $this->groups[$group->key] = $group;
    }

    public function groups(): array
    {
        return $this->groups;
    }

    public function permissions(): array
    {
        return $this->collect(fn (PermissionGroup $group): bool => true);
    }

    public function permissionsForScope(AccessScope $scope): array
    {
        return $this->collect(fn (PermissionGroup $group): bool => $group->scope === $scope);
    }

    /**
     * @param  callable(PermissionGroup): bool  $filter
     * @return array<int, string>
     */
    private function collect(callable $filter): array
    {
        $names = [];

        foreach ($this->groups as $group) {
            if ($filter($group)) {
                array_push($names, ...$group->permissions());
            }
        }

        return array_values(array_unique($names));
    }
}
