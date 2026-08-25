<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyFilament\Resources;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Taxonomy\Models\Taxonomy;

final class TaxonomyResource extends Resource
{
    #[\Override]
    protected static ?string $model = Taxonomy::class;

    #[\Override]
    protected static ?string $slug = 'cms-taxonomies';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required(), TextInput::make('name')->required(), Textarea::make('description'), Checkbox::make('hierarchical'), Checkbox::make('exclusive')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('terms_count')->counts('terms'), TextColumn::make('hierarchical')->badge()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListTaxonomies::route('/')];
    }
}
