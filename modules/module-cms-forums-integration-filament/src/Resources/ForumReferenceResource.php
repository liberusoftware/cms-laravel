<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ForumsIntegration\Models\ForumReference;
use Liberu\Cms\ForumsIntegrationFilament\Resources\Pages\ListForumReferences;

final class ForumReferenceResource extends Resource
{
    #[\Override]
    protected static ?string $model = ForumReference::class;

    #[\Override]
    protected static ?string $slug = 'cms-forum-references';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('provider')->required()->maxLength(180), TextInput::make('external_type')->required()->maxLength(180), TextInput::make('external_id')->required()->maxLength(180), TextInput::make('url')->url()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('provider')->badge()->searchable(), TextColumn::make('external_type'), TextColumn::make('external_id')->searchable(), TextColumn::make('last_activity_at')->dateTime()->sortable(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListForumReferences::route('/')];
    }
}
