<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ConfigurationManagement\Models\ConfigurationRelease;

final class ConfigurationReleaseResource extends Resource
{
    #[\Override]
    protected static ?string $model = ConfigurationRelease::class;

    #[\Override]
    protected static ?string $slug = 'cms-configuration-management';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('version')->required()->maxLength(80), TextInput::make('environment')->required()->maxLength(80), KeyValue::make('payload')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('version')->sortable(), TextColumn::make('environment'), TextColumn::make('status')->badge(), TextColumn::make('checksum'), TextColumn::make('created_at')->dateTime()->sortable()])->defaultSort('created_at', 'desc');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListConfigurationReleases::route('/')];
    }
}
