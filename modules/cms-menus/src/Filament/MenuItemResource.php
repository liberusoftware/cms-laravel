<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Menus\Filament\Pages\ListMenuItems;
use Liberu\Cms\Menus\Models\MenuItem;
use UnitEnum;

/**
 * Admin surface for individual menu items: the links within a menu, optionally
 * nested and permission-gated. Owned by the module.
 */
final class MenuItemResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = MenuItem::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-menu-items';

    #[\Override]
    protected static ?string $navigationLabel = 'Menu Items';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'label';

    protected static function cmsPermissionKey(): string
    {
        return 'menus';
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->schema([
                    Select::make('menu_id')
                        ->relationship('menu', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Menu'),
                    Select::make('parent_id')
                        ->relationship('parent', 'label')
                        ->searchable()
                        ->preload()
                        ->label('Parent item'),
                    TextInput::make('label')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('url')
                        ->required()
                        ->default('#')
                        ->maxLength(255),
                    TextInput::make('sort')
                        ->integer()
                        ->default(0)
                        ->required(),
                    TextInput::make('permission')
                        ->maxLength(255)
                        ->helperText('Optional permission name required to see this item.'),
                ]),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('menu.name')
                    ->badge()
                    ->sortable(),
                TextColumn::make('url')
                    ->toggleable(),
                TextColumn::make('permission')
                    ->badge()
                    ->placeholder('public')
                    ->toggleable(),
                TextColumn::make('sort')
                    ->sortable(),
            ])
            ->defaultSort('sort')
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
            'index' => ListMenuItems::route('/'),
        ];
    }
}
