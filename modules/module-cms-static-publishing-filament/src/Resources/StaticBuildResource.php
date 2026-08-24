<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\StaticPublishing\Models\StaticBuild;

final class StaticBuildResource extends Resource
{
    protected static ?string $model = StaticBuild::class;

    protected static ?string $slug = 'cms-static-publishing';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('site_key'), TextInput::make('kind'), TextInput::make('deployment')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('site_key'), TextColumn::make('kind')->badge(), TextColumn::make('state')->badge(), TextColumn::make('deployment'), TextColumn::make('finished_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListStaticBuilds::route('/')];
    }
}
