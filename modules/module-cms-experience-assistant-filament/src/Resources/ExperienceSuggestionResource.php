<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ExperienceAssistant\Models\ExperienceSuggestion;
use Liberu\Cms\ExperienceAssistantFilament\Resources\Pages\ListExperienceSuggestions;

final class ExperienceSuggestionResource extends Resource
{
    #[\Override]
    protected static ?string $model = ExperienceSuggestion::class;

    #[\Override]
    protected static ?string $slug = 'cms-experience-assistant-suggestions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('surface')->required()->maxLength(180), KeyValue::make('definition')->required(), KeyValue::make('constraints'), KeyValue::make('diagnostics')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('surface')->searchable()->sortable(), TextColumn::make('status')->badge(), TextColumn::make('reviewer_key'), TextColumn::make('approved_at')->dateTime(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListExperienceSuggestions::route('/')];
    }
}
