<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;

final class WordPressMigrationResource extends Resource
{
    #[\Override]
    protected static ?string $model = WordPressMigration::class;

    #[\Override]
    protected static ?string $slug = 'cms-wordpress-migrations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('source_url')->url(), TextInput::make('status')->disabled(), TextInput::make('total_records')->disabled(), TextInput::make('processed_records')->disabled(), TextInput::make('failed_records')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('public_id')->copyable(), TextColumn::make('source_url')->limit(40), TextColumn::make('status')->badge(), TextColumn::make('processed_records'), TextColumn::make('total_records'), TextColumn::make('failed_records')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListWordPressMigrations::route('/')];
    }
}
