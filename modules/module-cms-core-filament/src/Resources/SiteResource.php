<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Core\Models\Site;
use UnitEnum;

final class SiteResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static function cmsPermissionKey(): string
    {
        return 'core';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->required()->maxLength(255),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('domain')->url()->maxLength(255),
            TextInput::make('default_locale')->required()->maxLength(16),
            TextInput::make('timezone')->required()->maxLength(64),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('key')->searchable(),
            TextColumn::make('domain')->searchable(),
            TextColumn::make('channels_count')->counts('channels'),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSites::route('/')];
    }
}
