<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\EventsContent\Models\Event;

final class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $slug = 'cms-events-content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required(), TextInput::make('title')->required(), Textarea::make('description'), DateTimePicker::make('starts_at')->required(), DateTimePicker::make('ends_at')->required(), TextInput::make('timezone')->required(), TextInput::make('venue_id')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('starts_at')->dateTime(), TextColumn::make('status')->badge(), TextColumn::make('venue.name')->label('Venue')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListEvents::route('/')];
    }
}
