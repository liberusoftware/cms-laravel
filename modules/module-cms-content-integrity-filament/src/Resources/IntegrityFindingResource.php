<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentIntegrity\Models\IntegrityFinding;

final class IntegrityFindingResource extends Resource
{
    #[\Override]
    protected static ?string $model = IntegrityFinding::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind'), TextColumn::make('subject_key')->searchable(), TextColumn::make('severity'), TextColumn::make('status'), TextColumn::make('message')]);
    }
}
