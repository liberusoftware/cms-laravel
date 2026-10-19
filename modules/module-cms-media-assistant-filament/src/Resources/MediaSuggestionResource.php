<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\MediaAssistant\Models\MediaSuggestion;
use Liberu\Cms\MediaAssistantFilament\Resources\Pages\ListMediaSuggestions;

final class MediaSuggestionResource extends Resource
{
    #[\Override]
    protected static ?string $model = MediaSuggestion::class;

    #[\Override]
    protected static ?string $slug = 'cms-media-assistant-suggestions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('asset_key')->required()->maxLength(500), TextInput::make('kind')->required(), Textarea::make('value')->required(), TextInput::make('provider')->required()->maxLength(120), TextInput::make('model')->maxLength(120), TextInput::make('confidence')->numeric()->minValue(0)->maxValue(1), Textarea::make('provenance')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('asset_key')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('provider'), TextColumn::make('status')->badge(), TextColumn::make('confidence'), TextColumn::make('created_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListMediaSuggestions::route('/')];
    }
}
