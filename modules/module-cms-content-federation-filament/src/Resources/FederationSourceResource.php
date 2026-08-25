<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentFederation\Models\FederationSource;

final class FederationSourceResource extends Resource
{
    protected static ?string $model = FederationSource::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('adapter'), TextColumn::make('status'), TextColumn::make('last_succeeded_at')->dateTime()]);
    }
}
