<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Search\Models\SearchAnalytic;

final class SearchAnalyticResource extends Resource
{
    #[\Override]
    protected static ?string $model = SearchAnalytic::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('query')->searchable(), TextColumn::make('result_count'), TextColumn::make('duration_ms'), TextColumn::make('source'), TextColumn::make('created_at')->dateTime()]);
    }
}
