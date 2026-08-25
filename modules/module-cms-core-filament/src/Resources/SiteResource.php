<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Actions\CoreMutationService;
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
        ])->defaultSort('name')->recordActions([
            EditAction::make()->using(fn (Site $record, array $data): Site => app(CoreMutationService::class)->updateSite($record, $data)),
            DeleteAction::make()->using(function (Site $record): void {
                app(CoreMutationService::class)->deleteSite($record);
            }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSites::route('/')];
    }
}
