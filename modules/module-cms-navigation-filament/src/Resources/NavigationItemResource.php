<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Menus\Models\MenuItem;
use UnitEnum;

final class NavigationItemResource extends Resource
{
    #[\Override]
    protected static ?string $model = MenuItem::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-navigation-items';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('menu_id')->relationship('menu', 'name')->required()->searchable()->preload(),
            TextInput::make('label')->required()->maxLength(255),
            TextInput::make('url')->maxLength(2048),
            Select::make('link_type')->options(['custom' => 'Custom', 'content' => 'Content', 'system' => 'System'])->required(),
            TextInput::make('content_id')->maxLength(255),
            TextInput::make('system_route')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('label')->searchable()->sortable(),
            TextColumn::make('menu.name')->sortable(),
            TextColumn::make('link_type')->badge(),
        ]);
    }
}
