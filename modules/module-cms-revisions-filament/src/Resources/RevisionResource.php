<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Content\Revisions\Revision;

final class RevisionResource extends Resource
{
    protected static ?string $model = Revision::class;

    protected static ?string $slug = 'cms-revisions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('revisionable_type'), TextColumn::make('revisionable_id'), TextColumn::make('revision_number')->sortable(), TextColumn::make('branch'), TextColumn::make('user_id'), TextColumn::make('created_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListRevisions::route('/')];
    }
}
