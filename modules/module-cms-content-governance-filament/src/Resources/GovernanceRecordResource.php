<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentGovernance\Models\GovernanceRecord;

final class GovernanceRecordResource extends Resource
{
    #[\Override]
    protected static ?string $model = GovernanceRecord::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_key')->searchable(), TextColumn::make('classification'), TextColumn::make('review_due_at')->dateTime(), TextColumn::make('legal_hold')->boolean()]);
    }
}
