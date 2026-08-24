<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\SecurityOperations\Models\SecurityOperation;

final class SecurityOperationResource extends Resource
{
    protected static ?string $model = SecurityOperation::class;

    protected static ?string $slug = 'cms-security-operations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind'), TextColumn::make('subject'), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSecurityOperations::route('/')];
    }
}
