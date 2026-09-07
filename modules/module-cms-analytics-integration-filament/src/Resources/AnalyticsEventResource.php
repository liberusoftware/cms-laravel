<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AnalyticsEventResource extends Resource
{
    #[\Override]
    protected static ?string $slug = 'analytics-events';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('event_type')->options(['content' => 'Content', 'view' => 'View', 'conversion' => 'Conversion'])->required(), TextInput::make('event_name')->required(), TextInput::make('idempotency_key')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('event_type')->badge(), TextColumn::make('event_name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('occurred_at')->dateTime()]);
    }
}
