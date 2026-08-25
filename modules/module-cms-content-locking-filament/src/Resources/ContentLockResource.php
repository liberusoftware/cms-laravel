<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentLocking\Models\ContentLock;

final class ContentLockResource extends Resource
{
    #[\Override]
    protected static ?string $model = ContentLock::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_key')->searchable(), TextColumn::make('holder_id'), TextColumn::make('expires_at')->dateTime()]);
    }
}
