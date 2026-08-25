<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\DisplayModes\Models\DisplayMode;

final class DisplayModeResource extends Resource
{
    protected static ?string $model = DisplayMode::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('content_type'), TextColumn::make('mode_type'), TextColumn::make('active')->boolean()]);
    }
}
