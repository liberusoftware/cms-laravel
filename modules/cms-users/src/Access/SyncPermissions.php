<?php

declare(strict_types=1);

namespace Liberu\Cms\Users\Access;

use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Spatie\Permission\Models\Permission;

/**
 * Materialises every module-declared permission into the Spatie backend.
 *
 * Spatie is an internal implementation detail of the Users module (Golden Rule
 * 2d); no other module references it. Idempotent via findOrCreate.
 */
final readonly class SyncPermissions
{
    public function __construct(
        private PermissionRegistrarInterface $registrar,
        private string $guard = 'web',
    ) {}

    /**
     * @return array<int, string> The permission names that now exist.
     */
    public function __invoke(): array
    {
        $names = $this->registrar->permissions();

        foreach ($names as $name) {
            Permission::findOrCreate($name, $this->guard);
        }

        return $names;
    }
}
