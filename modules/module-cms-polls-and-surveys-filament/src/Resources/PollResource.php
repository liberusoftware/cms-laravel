<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use UnitEnum;

final class PollResource extends Resource
{
    #[\Override]
    protected static ?string $model = Poll::class;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-polls-and-surveys';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->required(), TextInput::make('key')->required()->alphaDash(), Toggle::make('active'), Toggle::make('allow_anonymous')->default(true), Toggle::make('allow_multiple'), Toggle::make('results_public')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('key'), TextColumn::make('questions_count')->counts('questions'), IconColumn::make('active')->boolean()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListPolls::route('/')];
    }
}
