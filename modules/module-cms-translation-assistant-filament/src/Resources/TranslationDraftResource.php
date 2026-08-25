<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantFilament\Resources;

use Filament\Forms\Components\{Textarea,TextInput};
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;

final class TranslationDraftResource extends Resource
{
    protected static ?string $model = TranslationDraft::class;
    protected static ?string $slug = 'cms-translation-drafts';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('subject_type')->required(), TextInput::make('subject_id')->required(), TextInput::make('source_locale')->required(), TextInput::make('target_locale')->required(), Textarea::make('source_text')->required(), Textarea::make('translated_text')->required(), TextInput::make('confidence')->numeric()->required(), TextInput::make('provider')->required(), TextInput::make('model')->required()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('target_locale'), TextColumn::make('confidence'), TextColumn::make('status')->badge(), TextColumn::make('provider')]); }
    /** @return array<string, PageRegistration> */ public static function getPages(): array { return ['index' => Pages\ListTranslationDrafts::route('/')]; }
}
