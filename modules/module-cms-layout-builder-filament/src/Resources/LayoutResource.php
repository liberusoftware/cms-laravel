<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\LayoutBuilder\Models\Layout;

final class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static ?string $slug = 'cms-layout-builder';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('target_type'), TextColumn::make('target_id'), TextColumn::make('status')->badge()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListLayouts::route('/')];
    }
}
