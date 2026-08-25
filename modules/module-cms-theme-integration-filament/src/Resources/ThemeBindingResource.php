<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;

final class ThemeBindingResource extends Resource
{
    #[\Override]
    protected static ?string $model = ThemeBinding::class;

    #[\Override]
    protected static ?string $slug = 'cms-theme-bindings';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('site_key')->required(), TextInput::make('channel_key'), TextInput::make('theme_key')->required(), TextInput::make('fallback_theme_key')->default('default')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('site_key')->searchable(), TextColumn::make('channel_key'), TextColumn::make('theme_key')->badge(), TextColumn::make('fallback_theme_key'), TextColumn::make('active')->badge()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListThemeBindings::route('/')];
    }
}
