<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CacheEntryResource extends Resource
{
    protected static ?string $slug = 'cache-entries';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('cache_key')->required(), Select::make('cache_type')->options(['page' => 'Page', 'render' => 'Render', 'query' => 'Query', 'object' => 'Object'])->required(), TextInput::make('ttl_seconds')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('cache_key')->searchable(), TextColumn::make('cache_type')->badge(), TextColumn::make('status')->badge(), TextColumn::make('hits'), TextColumn::make('misses')]);
    }
}
