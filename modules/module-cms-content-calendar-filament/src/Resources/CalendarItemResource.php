<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentCalendar\Models\CalendarItem;

final class CalendarItemResource extends Resource
{
    protected static ?string $model = CalendarItem::class;

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('channel'), TextColumn::make('site'), TextColumn::make('starts_at')->dateTime(), TextColumn::make('status')]);
    }
}
