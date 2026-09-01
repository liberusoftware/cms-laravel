<?php

namespace Liberu\Foundation\ModuleManagerFilament\Pages;

use Filament\Pages\Page;
use Liberu\Foundation\ModuleManager\Manifest;
use Liberu\Foundation\ModuleManager\ModuleRegistry;
use Liberu\Foundation\Observability\Contracts\ObservabilityActor;

final class FoundationOperations extends Page
{
    #[\Override]
    protected string $view = 'module-manager-filament::pages.foundation-operations';

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    #[\Override]
    protected static ?string $navigationLabel = 'Foundation Operations';

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public array $modules = [];

    public function mount(ModuleRegistry $registry): void
    {
        $this->modules = array_map(fn (Manifest $manifest) => $manifest->toArray(), $registry->all());
    }

    public static function canAccess(): bool
    {
        $actor = auth()->user();

        return $actor instanceof ObservabilityActor && $actor->isAdmin();
    }
}
