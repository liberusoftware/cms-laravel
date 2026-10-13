<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\EditorialContent\Models\EditorialPost;
use Liberu\Cms\EditorialContentFilament\Resources\Pages\ListEditorialPosts;

final class EditorialPostResource extends Resource
{
    #[\Override]
    protected static ?string $model = EditorialPost::class;

    #[\Override]
    protected static ?string $slug = 'cms-editorial-posts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Post')->schema([TextInput::make('slug')->required()->maxLength(200), TextInput::make('title')->required()->maxLength(240), Textarea::make('excerpt'), Textarea::make('body')->rows(12), Toggle::make('featured')])]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('slug')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListEditorialPosts::route('/')];
    }
}
