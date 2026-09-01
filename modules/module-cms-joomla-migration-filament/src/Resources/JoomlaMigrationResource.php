<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\MigrationFramework\Models\MigrationJob;

final class JoomlaMigrationResource extends Resource
{
    #[\Override]
    protected static ?string $model = MigrationJob::class;

    #[\Override]
    protected static ?string $slug = 'cms-joomla-migrations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('public_id'), TextColumn::make('status')->badge(), TextColumn::make('total_records'), TextColumn::make('processed_records'), TextColumn::make('failed_records')])->modifyQueryUsing(fn ($query) => $query->where('source', 'joomla'));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListJoomlaMigrations::route('/')];
    }
}
