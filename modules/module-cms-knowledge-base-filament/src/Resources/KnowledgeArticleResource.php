<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeArticle;
use Liberu\Cms\KnowledgeBaseFilament\Resources\Pages\ListKnowledgeArticles;

final class KnowledgeArticleResource extends Resource
{
    #[\Override]
    protected static ?string $model = KnowledgeArticle::class;

    #[\Override]
    protected static ?string $slug = 'cms-knowledge-base';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('slug')->required()->maxLength(180), TextInput::make('title')->required()->maxLength(240), Textarea::make('body')->required()->rows(14), TextInput::make('parent_id')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('slug')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListKnowledgeArticles::route('/')];
    }
}
