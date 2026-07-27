<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Access;

/**
 * A module's declaration of the permissions it owns.
 *
 * A module announces one or more groups (e.g. "pages" with abilities
 * view/create/update/delete/publish) and the platform materialises the
 * fully-qualified permission names (`pages.view`, …) into the permission
 * backend. Consumers authorize against those names via AccessControlInterface.
 *
 * @api This class is part of the public extension API.
 */
final readonly class PermissionGroup
{
    /**
     * @param  string  $key  Machine key, e.g. "pages".
     * @param  string  $label  Human label for admin surfaces.
     * @param  array<int, string>  $abilities  Bare abilities, e.g. ["view", "publish"].
     */
    public function __construct(
        public string $key,
        public string $label,
        public AccessScope $scope,
        public array $abilities,
    ) {}

    /**
     * Fully-qualified permission names, e.g. ["pages.view", "pages.publish"].
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return array_map(fn (string $ability): string => "{$this->key}.{$ability}", $this->abilities);
    }
}
