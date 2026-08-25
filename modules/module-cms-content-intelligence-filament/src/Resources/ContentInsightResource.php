<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentIntelligence\Models\ContentInsight;

final class ContentInsightResource extends Resource
{
    protected static ?string $model = ContentInsight::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('metric'), TextColumn::make('subject_key')->searchable(), TextColumn::make('score'), TextColumn::make('severity'), TextColumn::make('status')]);
    }
}
