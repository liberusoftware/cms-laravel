# Liberu CMS — Admin

## Repository

Source, issues, and release history: https://github.com/liberusoftware/module-cms-admin

Composer package: https://packagist.org/packages/liberusoftware/module-cms-admin

The administrative surface for Liberu CMS. It ships a Filament panel **plugin** that
exposes the CMS to editors and administrators without putting any feature code in the
host application.

## What it provides

| Surface | Consumes | Purpose |
| --- | --- | --- |
| **Modules** page | `ModuleRegistryInterface`, `ModuleManagerInterface`, `AccessControlInterface` | Review every registered module with its dependency graph and enable/disable optional ones. The manager enforces the safety rules — foundational modules stay on, and a module with enabled dependents cannot be disabled. |

## Permissions

The module declares one permission group via `PermissionRegistrarInterface`:

- `modules.view` — see the Modules page.
- `modules.manage` — enable or disable a module.

Run `php artisan cms:sync-permissions` (from `cms-users`) to materialise them.

## Wiring it into a panel

The admin surface is opt-in. Register the plugin on any Filament panel:

```php
use Liberu\Cms\Admin\Filament\CmsAdminPlugin;

$panel->plugins([
    CmsAdminPlugin::make(),
]);
```

Removing that line — or the package — removes the entire admin surface, per the
removable-module rule.

## Dependencies

Depends only on `cms-contracts` and `cms-core`. It never imports another module,
so it can be enabled or removed independently of the content modules it administers.
