<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;

final class PwaConfigurationResource extends Resource
{
    protected static ?string $model = PwaConfiguration::class;

    protected static ?string $slug = 'cms-offline-and-pwa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('site_key')->required(), TextInput::make('name')->required(), TextInput::make('short_name')->maxLength(12)->required(), TextInput::make('start_url')->default('/'), TextInput::make('offline_url')->default('/offline'), TextInput::make('theme_color'), TextInput::make('background_color'), TextInput::make('icon_url')->url(), Textarea::make('cache_policy')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('site_key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('service_worker_version'), TextColumn::make('last_updated_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListPwaConfigurations::route('/')];
    }
}
