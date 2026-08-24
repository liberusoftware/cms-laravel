<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Menus\Filament\Pages\ListMenus;
use Liberu\Cms\Menus\Models\Menu;
use UnitEnum;

/**
 * Admin surface for the Menus module: named navigation menus assigned to a
 * location. Menu items are managed through the Menu Items resource. Owned by the
 * module.
 */
final class MenuResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = Menu::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-menus';

    #[\Override]
    protected static ?string $navigationLabel = 'Menus';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    protected static function cmsPermissionKey(): string
    {
        return 'menus';
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('location')
                ->required()
                ->maxLength(255)
                ->helperText('Where the menu renders, e.g. "header", "footer", "sidebar".'),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
        ];
    }
}
