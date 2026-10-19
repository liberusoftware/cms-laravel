<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\EditorialWorkflow\Models\EditorialWorkflow;
use Liberu\Cms\EditorialWorkflowFilament\Resources\Pages\ListEditorialWorkflows;

final class EditorialWorkflowResource extends Resource
{
    #[\Override]
    protected static ?string $model = EditorialWorkflow::class;

    #[\Override]
    protected static ?string $slug = 'cms-editorial-workflows';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->maxLength(255), TextInput::make('name')->required()->maxLength(255), TextInput::make('initial_state')->required()->default('draft')->maxLength(255)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable()->sortable(), TextColumn::make('name')->searchable(), TextColumn::make('initial_state')->badge(), TextColumn::make('states_count')->counts('states'), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListEditorialWorkflows::route('/')];
    }
}
