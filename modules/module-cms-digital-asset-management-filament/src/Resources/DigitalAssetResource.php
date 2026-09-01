<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\DigitalAssetManagement\Models\DigitalAsset;

final class DigitalAssetResource extends Resource
{
    #[\Override]
    protected static ?string $model = DigitalAsset::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('asset_type'), TextColumn::make('license'), TextColumn::make('status'), TextColumn::make('approved'), TextColumn::make('expires_at')->dateTime()]);
    }
}
