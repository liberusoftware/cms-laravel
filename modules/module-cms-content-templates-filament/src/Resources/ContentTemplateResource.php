<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentTemplates\Models\ContentTemplate;

final class ContentTemplateResource extends Resource
{
    #[\Override]
    protected static ?string $model = ContentTemplate::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('content_type'), TextColumn::make('version'), TextColumn::make('published')->boolean(), TextColumn::make('locked')->boolean(), TextColumn::make('rollout_percent')]);
    }
}
