<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\BlockEditor\Models\BlockDocument;

final class BlockDocumentResource extends Resource
{
    protected static ?string $model = BlockDocument::class;

    protected static ?string $slug = 'block-documents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject_type')->required(), TextInput::make('subject_id')->required(), TextInput::make('version')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('version'), TextColumn::make('locked')->badge()]);
    }
}
