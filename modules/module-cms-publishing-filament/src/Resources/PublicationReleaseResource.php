<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Publishing\Models\PublicationRelease;

final class PublicationReleaseResource extends Resource
{
    protected static ?string $model = PublicationRelease::class;

    protected static ?string $slug = 'cms-publishing';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash(), DateTimePicker::make('publish_at'), DateTimePicker::make('embargo_until'), DateTimePicker::make('expires_at'), DateTimePicker::make('review_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('state')->badge(), TextColumn::make('publish_at')->dateTime(), TextColumn::make('expires_at')->dateTime()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListPublicationReleases::route('/')];
    }
}
