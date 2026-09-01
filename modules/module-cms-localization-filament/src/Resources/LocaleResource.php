<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Localization\Models\Locale;
use Liberu\Cms\LocalizationFilament\Resources\Pages\ListLocales;

final class LocaleResource extends Resource
{
    protected static ?string $model = Locale::class;

    protected static ?string $slug = 'cms-localization-locales';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('locale')->required()->maxLength(35), TextInput::make('fallback_locale')->maxLength(35), Select::make('direction')->options(['ltr' => 'Left to right', 'rtl' => 'Right to left'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('locale')->searchable()->sortable(), TextColumn::make('fallback_locale'), TextColumn::make('direction')->badge(), TextColumn::make('enabled')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListLocales::route('/')];
    }
}
