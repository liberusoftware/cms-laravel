<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteFilament\Resources;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\MultisiteFilament\Resources\MultisiteSiteResource\Pages\ListSites;
use UnitEnum;

final class MultisiteSiteResource extends Resource
{
    #[\Override]
    protected static ?string $model = Site::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-multisite';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->maxLength(100), TextInput::make('name')->required()->maxLength(255), TextInput::make('domain')->maxLength(255), TextInput::make('status')->required()->default('active')->maxLength(32)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable()->sortable(), TextColumn::make('name')->searchable(), TextColumn::make('domain'), TextColumn::make('status')->badge()->sortable()])->headerActions([CreateAction::make()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListSites::route('/')];
    }
}
