<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\DocumentManagement\Models\Document;

final class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('mime_type'), TextColumn::make('status')->badge(), TextColumn::make('retention_until')->dateTime()]);
    }
}
