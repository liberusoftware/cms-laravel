<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Filament;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class CollectionResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Collection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static function cmsPermissionKey(): string
    {
        return 'collections';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255)->helperText('Leave blank to generate from the name.'),
            TextInput::make('type')->required()->default('record')->maxLength(64),
            Textarea::make('description')->columnSpanFull(),
            Textarea::make('schema')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                ->json()
                ->helperText('Optional JSON field definition.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('slug')->searchable(), TextColumn::make('items_count')->counts('items'), TextColumn::make('updated_at')->dateTime()->sortable()])->defaultSort('created_at', 'desc');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListCollections::route('/')];
    }
}
