<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentAccess\Models\AccessRule;

final class AccessRuleResource extends Resource
{
    protected static ?string $model = AccessRule::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_key')->searchable(), TextColumn::make('visibility')]);
    }
}
