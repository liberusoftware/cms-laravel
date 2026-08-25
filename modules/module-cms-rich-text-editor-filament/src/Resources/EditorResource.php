<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorFilament\Resources;

use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class EditorResource extends Resource
{
    #[\Override]
    protected static ?string $slug = 'cms-rich-text-editor';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([RichEditor::make('content')->label('Content')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [];
    }
}
