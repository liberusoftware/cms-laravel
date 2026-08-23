<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Liberu\Cms\Contracts\Theme\ThemeManagerInterface;
use Liberu\Cms\Themes\Exceptions\UnknownTheme;
use UnitEnum;

/**
 * Admin surface for the Themes module: lists every registered theme with its
 * inheritance parent and lets an administrator activate one. Themes are a
 * registry (not an Eloquent model), so this is a page rather than a resource.
 */
class ThemeManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected string $view = 'cms-themes::filament.pages.theme-management';

    protected static ?string $title = 'Themes';

    protected static ?string $navigationLabel = 'Themes';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'CMS';
    }

    /**
     * @return array<int, array{key: string, name: string, parent: string|null, active: bool}>
     */
    public function themes(): array
    {
        $manager = app(ThemeManagerInterface::class);
        $activeKey = $manager->active()?->key();

        $rows = [];

        foreach ($manager->all() as $theme) {
            $rows[] = [
                'key' => $theme->key(),
                'name' => $theme->name(),
                'parent' => $theme->parent(),
                'active' => $theme->key() === $activeKey,
            ];
        }

        usort($rows, fn (array $a, array $b): int => $a['name'] <=> $b['name']);

        return $rows;
    }

    public function activate(string $key): void
    {
        try {
            app(ThemeManagerInterface::class)->setActive($key);
        } catch (UnknownTheme $exception) {
            Notification::make()
                ->title('That theme could not be activated.')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title("Activated the \"{$key}\" theme.")
            ->success()
            ->send();
    }
}
