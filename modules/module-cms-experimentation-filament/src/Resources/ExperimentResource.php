<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Experimentation\Models\Experiment;

final class ExperimentResource extends Resource
{
    #[\Override]
    protected static ?string $model = Experiment::class;

    #[\Override]
    protected static ?string $slug = 'cms-experimentation';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required(), TextInput::make('name')->required(), TextInput::make('type')->required(), TextInput::make('allocation_percentage')->numeric()->required(), Textarea::make('goals')->json(), Textarea::make('guardrails')->json(), Textarea::make('analysis_policy')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('type'), TextColumn::make('status')->badge(), TextColumn::make('winner_variant_key')->label('Winner')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListExperiments::route('/')];
    }
}
