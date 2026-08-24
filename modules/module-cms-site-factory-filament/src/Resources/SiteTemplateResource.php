<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\SiteFactory\Models\SiteTemplate;

final class SiteTemplateResource extends Resource
{
    protected static ?string $model = SiteTemplate::class;

    protected static ?string $slug = 'cms-site-factory';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash(), TextInput::make('name')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key'), TextColumn::make('name'), TextColumn::make('active')->badge()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSiteTemplates::route('/')];
    }
}
