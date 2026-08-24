<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\RelatedContent\Models\RelatedContent;

final class RelatedContentResource extends Resource
{
    protected static ?string $model = RelatedContent::class;

    protected static ?string $slug = 'cms-related-content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('source_type')->required(), TextInput::make('source_id')->numeric()->required(), TextInput::make('target_type')->required(), TextInput::make('target_id')->numeric()->required(), TextInput::make('mode')->required(), TextInput::make('score')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('source_type'), TextColumn::make('source_id'), TextColumn::make('target_type'), TextColumn::make('target_id'), TextColumn::make('mode')->badge(), TextColumn::make('score')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListRelatedContent::route('/')];
    }
}
