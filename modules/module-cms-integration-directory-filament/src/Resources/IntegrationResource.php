<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\IntegrationDirectory\Models\Integration;

final class IntegrationResource extends Resource
{
    #[\Override]
    protected static ?string $model = Integration::class;

    #[\Override]
    protected static ?string $slug = 'cms-integrations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('provider'), TextColumn::make('status')->badge(), TextColumn::make('health_status')->badge(), TextColumn::make('last_checked_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListIntegrations::route('/')];
    }
}
