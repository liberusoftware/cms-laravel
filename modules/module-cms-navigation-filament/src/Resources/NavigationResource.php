<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationFilament\Resources;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\NavigationFilament\Resources\NavigationResource\Pages\ListNavigation;
use UnitEnum;

final class NavigationResource extends Resource
{
    #[\Override]
    protected static ?string $model = Menu::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-navigation';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('location')->required()->maxLength(255),
            TextInput::make('variant')->required()->default('default')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('location')->sortable(),
            TextColumn::make('variant')->badge()->sortable(),
        ])->headerActions([CreateAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListNavigation::route('/')];
    }
}
