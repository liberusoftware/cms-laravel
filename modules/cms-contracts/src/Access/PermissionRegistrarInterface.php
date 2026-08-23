<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Access;

/**
 * The catalogue of permission groups declared by modules.
 *
 * Modules register their PermissionGroups during boot; the Users module reads
 * the catalogue to materialise the permissions into the backend and to render
 * them in admin surfaces. No module writes permissions directly.
 *
 * @api This interface is part of the public extension API.
 */
interface PermissionRegistrarInterface
{
    /**
     * Declare a group of permissions owned by a module.
     */
    public function register(PermissionGroup $group): void;

    /**
     * All registered groups, keyed by group key.
     *
     * @return array<string, PermissionGroup>
     */
    public function groups(): array;

    /**
     * Every fully-qualified permission name across all groups.
     *
     * @return array<int, string>
     */
    public function permissions(): array;

    /**
     * Permission names limited to a single scope.
     *
     * @return array<int, string>
     */
    public function permissionsForScope(AccessScope $scope): array;
}
