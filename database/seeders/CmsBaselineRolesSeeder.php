<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Liberu\Cms\Users\Access\SyncPermissions;

/**
 * Provisions the baseline CMS role set and maps it onto the module-declared
 * permissions.
 *
 * Permissions are materialised from the module registrars (never hand-listed
 * here), then four roles are given a sensible slice of them. Roles are scoped
 * to the active permission team so the seeder works with tenancy on or off; it
 * is idempotent, so re-running only re-syncs the mappings.
 *
 * The default role for a self-registered owner is `super_admin` (assigned when
 * a personal team is created); ticket 07 formalises the self-registration role
 * model (OWASP A04).
 */
final class CmsBaselineRolesSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = app(SyncPermissions::class)();

        $teamId = getPermissionsTeamId();

        $map = [
            'super_admin' => $permissions,
            'admin' => $permissions,
            'editor' => $this->withAbilities($permissions, ['view', 'create', 'update', 'delete'], ['modules', 'api-tokens', 'notification-logs']),
            'author' => $this->onlyGroups($this->withAbilities($permissions, ['view', 'create', 'update']), ['pages', 'posts', 'content-entries', 'forms', 'media']),
            'viewer' => $this->withAbilities($permissions, ['view']),
        ];

        foreach ($map as $roleName => $granted) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'team_id' => $teamId,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($granted);
        }
    }

    /**
     * Keep only permissions whose bare ability is in the allow-list, excluding
     * whole groups by key.
     *
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $abilities
     * @param  array<int, string>  $exceptGroups
     * @return array<int, string>
     */
    private function withAbilities(array $permissions, array $abilities, array $exceptGroups = []): array
    {
        return array_values(array_filter($permissions, function (string $permission) use ($abilities, $exceptGroups): bool {
            [$group, $ability] = array_pad(explode('.', $permission, 2), 2, '');

            return in_array($ability, $abilities, true) && ! in_array($group, $exceptGroups, true);
        }));
    }

    /**
     * Keep only permissions belonging to the given group keys.
     *
     * @param  array<int, string>  $permissions
     * @param  array<int, string>  $groups
     * @return array<int, string>
     */
    private function onlyGroups(array $permissions, array $groups): array
    {
        return array_values(array_filter($permissions, function (string $permission) use ($groups): bool {
            [$group] = explode('.', $permission, 2);

            return in_array($group, $groups, true);
        }));
    }
}
