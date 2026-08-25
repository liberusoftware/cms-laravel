<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Personalization\Models\Audience;
use UnitEnum;

final class AudienceResource extends Resource
{
    #[\Override]
    protected static ?string $model = Audience::class;

    #[\Override]
    protected static ?string $slug = 'cms-personalization-audiences';

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('key')->required()->alphaDash()->maxLength(255),
            KeyValue::make('rules')->label('Eligibility rules'),
            Toggle::make('requires_consent'),
            Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('key')->searchable(),
            TextColumn::make('variants_count')->counts('variants'),
            IconColumn::make('active')->boolean(),
        ])->defaultSort('name');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListAudiences::route('/'), 'edit' => Pages\EditAudience::route('/{record}/edit')];
    }
}
