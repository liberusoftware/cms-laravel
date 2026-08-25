<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Core\Models\Channel;
use UnitEnum;

final class ChannelResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = Channel::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRss;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static function cmsPermissionKey(): string
    {
        return 'core';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('site_id')->relationship('site', 'name')->required()->searchable()->preload(),
            TextInput::make('key')->required()->maxLength(255),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('type')->required()->maxLength(64),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('key')->searchable(),
            TextColumn::make('site.name')->label('Site')->sortable(),
            TextColumn::make('type')->badge(),
        ])->defaultSort('name')->recordActions([
            EditAction::make()->using(fn (Channel $record, array $data): Channel => app(CoreMutationService::class)->updateChannel($record, $data)),
            DeleteAction::make()->using(function (Channel $record): void {
                app(CoreMutationService::class)->deleteChannel($record);
            }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListChannels::route('/')];
    }
}
