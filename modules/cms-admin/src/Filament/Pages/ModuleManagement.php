<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Liberu\Cms\Admin\Filament\Support\ModuleView;
use Liberu\Cms\Contracts\Access\AccessControlInterface;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;
use Liberu\Cms\Core\Exceptions\ModuleDependencyException;
use UnitEnum;

/**
 * The control room for the module system: lists every registered module with
 * its dependency graph and lets an administrator enable or disable one, with the
 * manager enforcing the safety rules (foundational modules stay on; a module
 * with enabled dependents cannot be turned off).
 */
class ModuleManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected string $view = 'cms-admin::filament.pages.module-management';

    protected static ?string $title = 'Modules';

    protected static ?string $navigationLabel = 'Modules';

    public static function canAccess(): bool
    {
        return app(AccessControlInterface::class)->can('modules.view');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        $group = config('cms-admin.navigation_group', 'CMS');

        return is_string($group) ? $group : 'CMS';
    }

    /**
     * The modules to render, in a stable, dependency-first order.
     *
     * @return array<int, ModuleView>
     */
    public function modules(): array
    {
        $registry = app(ModuleRegistryInterface::class);
        $manager = app(ModuleManagerInterface::class);

        $views = [];

        foreach ($registry->all() as $module) {
            $key = $module->key();

            $views[] = new ModuleView(
                key: $key,
                name: $module->name(),
                version: $module->version(),
                enabled: $manager->isEnabled($key),
                foundational: $module->isFoundational(),
                dependencies: $module->dependencies(),
                dependents: $manager->dependentsOf($key),
            );
        }

        usort($views, fn (ModuleView $a, ModuleView $b): int => $a->name <=> $b->name);

        return $views;
    }

    public function enable(string $key): void
    {
        $this->authorizeManagement();

        try {
            app(ModuleManagerInterface::class)->enable($key);
        } catch (ModuleDependencyException $exception) {
            $this->failure($exception->getMessage());

            return;
        }

        Notification::make()
            ->title("Enabled the \"{$key}\" module.")
            ->success()
            ->send();
    }

    public function disable(string $key): void
    {
        $this->authorizeManagement();

        try {
            app(ModuleManagerInterface::class)->disable($key);
        } catch (ModuleDependencyException $exception) {
            $this->failure($exception->getMessage());

            return;
        }

        Notification::make()
            ->title("Disabled the \"{$key}\" module.")
            ->success()
            ->send();
    }

    private function authorizeManagement(): void
    {
        app(AccessControlInterface::class)->authorize('modules.manage');
    }

    private function failure(string $message): void
    {
        Notification::make()
            ->title('That change was rejected.')
            ->body($message)
            ->danger()
            ->send();
    }
}
