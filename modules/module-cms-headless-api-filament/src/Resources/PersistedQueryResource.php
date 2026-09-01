<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\HeadlessApi\Models\PersistedQuery;
use Liberu\Cms\HeadlessApiFilament\Resources\Pages\ListPersistedQueries;

final class PersistedQueryResource extends Resource
{
    #[\Override]
    protected static ?string $model = PersistedQuery::class;

    #[\Override]
    protected static ?string $slug = 'cms-headless-persisted-queries';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Textarea::make('query_body')->required()->rows(18)->maxLength(100000)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('query_hash')->searchable(), TextColumn::make('query_body')->limit(80), TextColumn::make('last_used_at')->dateTime(), TextColumn::make('created_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPersistedQueries::route('/')];
    }
}
