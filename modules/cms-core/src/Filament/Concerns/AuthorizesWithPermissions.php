<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Contracts\Access\AccessControlInterface;

/**
 * Gates a Filament resource behind its module-owned permissions.
 *
 * The resource names the permission group it belongs to (e.g. "pages") and this
 * trait maps each Filament authorization method onto a `<key>.<ability>` check
 * resolved through the platform {@see AccessControlInterface} — which the host's
 * permission backend (Shield/Spatie) populates. When the contract is unbound —
 * a headless install without the Users module — every action is denied, so the
 * resource fails safe rather than open.
 *
 * A resource may still override any individual method (e.g. a read-only resource
 * declaring `canCreate(): bool => false`); class methods win over trait methods.
 */
trait AuthorizesWithPermissions
{
    public static function canViewAny(): bool
    {
        return static::allowsCmsAction('view');
    }

    public static function canView(Model $record): bool
    {
        return static::allowsCmsAction('view');
    }

    public static function canCreate(): bool
    {
        return static::allowsCmsAction('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::allowsCmsAction('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::allowsCmsAction('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::allowsCmsAction('delete');
    }

    /**
     * The permission group key this resource is gated by, e.g. "pages".
     */
    abstract protected static function cmsPermissionKey(): string;

    protected static function allowsCmsAction(string $ability): bool
    {
        if (! app()->bound(AccessControlInterface::class)) {
            return false;
        }

        return app(AccessControlInterface::class)->can(static::cmsPermissionKey().'.'.$ability);
    }
}
